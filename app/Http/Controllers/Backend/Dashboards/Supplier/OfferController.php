<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\CreateOfferRequest;
use App\Http\Requests\Supplier\UpdateOfferRequest;
use App\Interfaces\Supplier\OfferRepositoryInterface;
use App\Models\SupplierSpecializedCategory;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    protected $offerRepository;

    public function __construct(OfferRepositoryInterface $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    public function index()
    {
        return view('backend.dashboards.supplier.pages.offers.index');
    }

    public function data()
    {
        return $this->offerRepository->data();
    }

    public function store(CreateOfferRequest $request)
    {
        try {
            $this->offerRepository->store($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Offer submitted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $offer = $this->offerRepository->show($id);
            return view('backend.dashboards.supplier.pages.offers.show', compact('offer'));
        } catch (\Exception $e) {
            return redirect()->route('supplier.offers.index')->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $offer = $this->offerRepository->show($id);
            return view('backend.dashboards.supplier.pages.offers.edit', compact('offer'));
        } catch (\Exception $e) {
            return redirect()->route('supplier.offers.index')->with('error', $e->getMessage());
        }
    }

    public function update(UpdateOfferRequest $request, $id)
    {
        try {
            $this->offerRepository->update($request->validated(), $id);
            return response()->json([
                'success' => true,
                'message' => 'Offer updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->offerRepository->destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Offer deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Available requests for suppliers to submit offers
    public function availableRequests()
    {
        return view('backend.dashboards.supplier.pages.available-requests.index');
    }

    public function availableRequestsData()
    {
        return $this->offerRepository->availableRequestsData();
    }

    public function showAvailableRequest($id)
    {
        try {
            $request = RequestModel::with(['categories', 'clinic'])->findOrFail($id);

            $supplierId = auth('supplier')->user()->supplier_id;
            // Get categories this supplier specializes in
            $supplierCategoryIds = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
                $q->where('suppliers.id', $supplierId);
            })->pluck('id')->toArray();

            $requestCategoryIds = $request->categories()->pluck('supplier_specialized_categories.id')->toArray();
            // Check if supplier specializes in any of the request categories
            $hasCommonCategory = !empty(array_intersect($supplierCategoryIds, $requestCategoryIds));

            if (!$hasCommonCategory) {
                return redirect()->route('supplier.available-requests.index')
                    ->with('error', 'You are not authorized to view this request.');
            }

            $hasOffer = $request->hasOfferFromSupplier($supplierId);

            return view('backend.dashboards.supplier.pages.available-requests.show', compact('request', 'hasOffer'));
        } catch (\Exception $e) {
            return redirect()->route('supplier.available-requests.index')->with('error', $e->getMessage());
        }
    }

    public function createOfferForRequest($requestId)
    {
        try {
            $request = RequestModel::with(['categories', 'clinic'])->findOrFail($requestId);

            $supplierId = auth('supplier')->user()->supplier_id;
            // Get categories this supplier specializes in
            $supplierCategoryIds = SupplierSpecializedCategory::whereHas('suppliers', function($q) use ($supplierId) {
                $q->where('suppliers.id', $supplierId);
            })->pluck('id')->toArray();

            $requestCategoryIds = $request->categories()->pluck('supplier_specialized_categories.id')->toArray();
            // Check if supplier specializes in any of the request categories
            $hasCommonCategory = !empty(array_intersect($supplierCategoryIds, $requestCategoryIds));

            if (!$hasCommonCategory) {
                return redirect()->route('supplier.available-requests.index')
                    ->with('error', 'You are not authorized to submit offer for this request.');
            }

            if ($request->hasOfferFromSupplier($supplierId)) {
                return redirect()->route('supplier.available-requests.show', $requestId)
                    ->with('error', 'You have already submitted an offer for this request.');
            }

            return view('backend.dashboards.supplier.pages.offers.create', compact('request'));
        } catch (\Exception $e) {
            return redirect()->route('supplier.available-requests.index')->with('error', $e->getMessage());
        }
    }
}
