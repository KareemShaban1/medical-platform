<?php

namespace App\Repository\Supplier;

use App\Interfaces\Supplier\OfferRepositoryInterface;
use App\Models\Offer;
use App\Models\Request;
use App\Models\SupplierSpecializedCategory;
use App\Notifications\Clinic\NewOfferNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfferRepository implements OfferRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $offers = Offer::with(['request.categories', 'request.clinic'])->mine()->latest();

        return datatables()->of($offers)
            ->addColumn('request_description', fn($item) => Str::limit($item->request->description, 50))
            ->addColumn('clinic_name', fn($item) => $item->request->clinic->name ?? 'N/A')
            ->addColumn('categories', fn($item) => $item->request->categories->pluck('name')->join(', ') ?? 'N/A')
            ->addColumn('final_price', fn($item) => number_format($item->final_price, 2))
            ->addColumn('status', fn($item) => $this->offerStatus($item))
            ->addColumn('delivery_time', fn($item) => $item->delivery_time->format('Y-m-d'))
            ->addColumn('action', fn($item) => $this->offerActions($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $data['supplier_id'] = auth('supplier')->user()->supplier_id;

            $requestModel = Request::findOrFail($data['request_id']);

            // Check if request is still open
            if (!$requestModel->canReceiveOffers()) {
                throw new \Exception('Request is no longer accepting offers');
            }

            // Check if supplier already has an offer for this request
            if ($requestModel->hasOfferFromSupplier($data['supplier_id'])) {
                throw new \Exception('You have already submitted an offer for this request');
            }

            // Check if supplier specializes in any of the request categories
            $supplierCategories = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($data) {
                $q->where('suppliers.id', $data['supplier_id']);
            })->pluck('id')->toArray();

            $requestCategories = $requestModel->categories()->pluck('supplier_specialized_categories.id')->toArray();

            $hasCommonCategory = !empty(array_intersect($supplierCategories, $requestCategories));

            if (!$hasCommonCategory) {
                throw new \Exception('You are not specialized in any of the categories for this request');
            }

            $offer = Offer::create($data);

            // Notify clinic users about new offer
            foreach ($requestModel->clinic->clinicUsers as $clinicUser) {
                $clinicUser->notify(new NewOfferNotification($offer));
            }

            return $offer;
        });
    }

    public function show($id)
    {
        return Offer::with(['request.categories', 'request.clinic', 'supplier'])->mine()->findOrFail($id);
    }

    public function update($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $offer = Offer::mine()->findOrFail($id);

            // Only allow updates if offer is still pending
            if ($offer->status !== Offer::STATUS_PENDING) {
                throw new \Exception('Cannot update non-pending offer');
            }

            // Check if request is still open
            if (!$offer->request->canReceiveOffers()) {
                throw new \Exception('Request is no longer accepting offers');
            }

            $data = $request;
            $offer->update($data);

            return $offer;
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $offer = Offer::mine()->findOrFail($id);

            // Only allow deletion if offer is pending
            if ($offer->status !== Offer::STATUS_PENDING) {
                throw new \Exception('Cannot delete non-pending offer');
            }

            $offer->delete();
            return $offer;
        });
    }

    public function getAvailableRequests()
    {
        return [];
    }

    public function availableRequestsData()
    {
        $supplierId = auth('supplier')->user()->supplier_id;

        // Get categories this supplier specializes in
        $categoryIds = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
            $q->where('suppliers.id', $supplierId);
        })->pluck('id');

        $requests = Request::with(['categories', 'clinic'])
            ->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('supplier_specialized_categories.id', $categoryIds);
            })
            ->where('status', Request::STATUS_OPEN)
            ->whereDoesntHave('offers', function ($query) use ($supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->latest();

        return datatables()->of($requests)
            ->addColumn('clinic_name', fn($item) => $item->clinic->name ?? 'N/A')
            ->addColumn('categories', fn($item) => $item->categories->pluck('name')->join(', ') ?: 'N/A')
            ->addColumn('description', fn($item) => Str::limit($item->description, 100))
            ->addColumn('quantity', fn($item) => $item->quantity)
            ->addColumn('timeline', fn($item) => $item->timeline ? $item->timeline->format('Y-m-d') : 'N/A')
            ->addColumn('attachments_count', fn($item) => count($item->attachments))
            ->addColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->availableRequestActions($item))
            ->rawColumns(['action'])
            ->make(true);
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function offerStatus($item): string
    {
        $badges = [
            'pending' => 'warning',
            'accepted' => 'success',
            'declined' => 'danger'
        ];

        $class = $badges[$item->status] ?? 'secondary';
        return '<span class="badge bg-' . $class . '">' . ucfirst($item->status) . '</span>';
    }

    private function offerActions($item): string
    {
        $showUrl = route('supplier.offers.show', $item->id);
        $actions = '<div class="d-flex gap-2">';
        $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-success" title="View"><i class="fa fa-eye"></i></a>';

        if ($item->status === Offer::STATUS_PENDING) {
            $actions .= '<button onclick="editOffer(' . $item->id . ')" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></button>';
            $actions .= '<button onclick="deleteOffer(' . $item->id . ')" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>';
        }

        $actions .= '</div>';
        return $actions;
    }

    private function availableRequestActions($item): string
    {
        $viewUrl = route('supplier.available-requests.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$viewUrl}" class="btn btn-sm btn-success" title="View Details"><i class="fa fa-eye"></i></a>
            <button onclick="submitOffer({$item->id})" class="btn btn-sm btn-primary" title="Submit Offer"><i class="fa fa-paper-plane"></i></button>
        </div>
        HTML;
    }
}
