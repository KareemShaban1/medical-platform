<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\RequestRepositoryInterface;
use App\Models\Request;
use App\Models\Offer;
use App\Models\SupplierSpecializedCategory;
use App\Models\Admin;
use App\Models\Supplier;
use App\Notifications\Supplier\NewRequestNotification;
use App\Notifications\Supplier\OfferAcceptedNotification;
use App\Notifications\Supplier\OfferDeclinedNotification;
use Illuminate\Support\Facades\DB;

class RequestRepository implements RequestRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $requests = Request::with(['categories', 'offers'])->mine()->latest();

        return datatables()->of($requests)
            ->addColumn('categories', fn($item) => $item->categories->pluck('name')->join(', ') ?: 'N/A')
            ->addColumn('offers_count', fn($item) => $item->offers->count())
            ->addColumn('status', fn($item) => $this->requestStatus($item))
            ->addColumn('timeline', fn($item) => $item->timeline ? $item->timeline->format('Y-m-d') : 'N/A')
            ->addColumn('action', fn($item) => $this->requestActions($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $data['clinic_id'] = auth('clinic')->user()->clinic_id;

            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);

            $requestModel = Request::create($data);

            // Attach categories to request
            if (!empty($categoryIds)) {
                $requestModel->categories()->attach($categoryIds);
            }

            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $requestModel->addMedia($attachment)->toMediaCollection('request_attachments');
                }
            }

            // Notify all suppliers in these categories
            $suppliers = Supplier::whereHas('specializedCategories', function ($query) use ($categoryIds) {
                $query->whereIn('supplier_specialized_categories.id', $categoryIds);
            })->get();


            foreach ($suppliers as $supplier) {
                // Notify all supplier users
                foreach ($supplier->supplierUsers as $supplierUser) {
                    $supplierUser->notify(new NewRequestNotification($requestModel));
                }
            }


            return $requestModel;
        });
    }

    public function show($id)
    {
        return Request::with(['categories', 'offers.supplier', 'clinic'])->mine()->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $requestModel = Request::mine()->findOrFail($id);

            // Only allow updates if request is still open
            if ($requestModel->status !== Request::STATUS_OPEN) {
                throw new \Exception('Cannot update closed or canceled request');
            }

            $data = $request;

            $categoryIds = $data['category_ids'] ?? [];
            unset($data['category_ids']);

            $requestModel->update($data);

            // Update categories
            if (!empty($categoryIds)) {
                $requestModel->categories()->sync($categoryIds);
            }

            if (isset($data['removed_attachments']) && !empty($data['removed_attachments'])) {
                foreach ($data['removed_attachments'] as $attachmentId) {
                    $requestModel->deleteMedia($attachmentId);
                }
            }

            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $requestModel->addMedia($attachment)->toMediaCollection('request_attachments');
                }
            }

            return $requestModel;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $request = Request::mine()->findOrFail($id);

            // Only allow deletion if no offers have been accepted
            if ($request->acceptedOffer) {
                throw new \Exception('Cannot delete request with accepted offer');
            }

            $request->delete();
            return $request;
        });
    }

    public function getCategories()
    {
        return SupplierSpecializedCategory::select('id', 'name_en', 'name_ar')
        ->get()
        ->map(function ($category) {
            return [
                'id' => $category->id,
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'name' => $category->name,
                'suppliers_count' => $category->suppliers()->count(),
            ];
        });
    }

    public function acceptOffer($requestId, $offerId)
    {
        return DB::transaction(function () use ($requestId, $offerId) {
            $request = Request::mine()->findOrFail($requestId);
            $offer = Offer::where('request_id', $requestId)->findOrFail($offerId);

            if (!$offer->canBeAccepted()) {
                throw new \Exception('Offer cannot be accepted');
            }

            // Accept the selected offer
            $offer->update(['status' => Offer::STATUS_ACCEPTED]);

            // Decline all other offers for this request
            $otherOffers = Offer::where('request_id', $requestId)
                ->where('id', '!=', $offerId)
                ->where('status', Offer::STATUS_PENDING)
                ->get();

            foreach ($otherOffers as $otherOffer) {
                $otherOffer->update(['status' => Offer::STATUS_DECLINED]);

                // Notify declined suppliers
                foreach ($otherOffer->supplier->supplierUsers as $supplierUser) {
                    $supplierUser->notify(new OfferDeclinedNotification($otherOffer));
                }
            }

            $request->update(['status' => Request::STATUS_CLOSED]);

            // Notify accepted supplier
            foreach ($offer->supplier->supplierUsers as $supplierUser) {
                $supplierUser->notify(new OfferAcceptedNotification($offer));
            }

            return $offer;
        });
    }

    public function cancelRequest($id)
    {
        return DB::transaction(function () use ($id) {
            $request = Request::mine()->findOrFail($id);

            if ($request->status !== Request::STATUS_OPEN) {
                throw new \Exception('Can only cancel open requests');
            }

            // Decline all pending offers
            $pendingOffers = $request->pendingOffers;
            foreach ($pendingOffers as $offer) {
                $offer->update(['status' => Offer::STATUS_DECLINED]);

                // Notify suppliers
                foreach ($offer->supplier->supplierUsers as $supplierUser) {
                    $supplierUser->notify(new OfferDeclinedNotification($offer));
                }
            }

            $request->update(['status' => Request::STATUS_CANCELED]);
            return $request;
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function requestStatus($item): string
    {
        $badges = [
            'open' => 'success',
            'closed' => 'primary',
            'canceled' => 'danger'
        ];

        $class = $badges[$item->status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($item->status) . '</span>';
    }

    private function requestActions($item): string
    {
        $showUrl = route('clinic.requests.show', $item->id);
        $actions = '<div class="d-flex gap-2">';
        $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';

        if ($item->status === Request::STATUS_OPEN) {
            $actions .= '<button onclick="editRequest(' . $item->id . ')" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>';
            $actions .= '<button onclick="cancelRequest(' . $item->id . ')" class="btn btn-sm btn-warning" title="Cancel"><i class="fa fa-ban"></i></button>';
        }

        if ($item->status !== Request::STATUS_CLOSED || !$item->acceptedOffer) {
            $actions .= '<button onclick="deleteRequest(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }

        $actions .= '</div>';
        return $actions;
    }
}
