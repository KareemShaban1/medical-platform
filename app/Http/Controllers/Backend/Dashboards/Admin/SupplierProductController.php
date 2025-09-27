<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\SupplierProductRepositoryInterface;
use App\Models\Product;
use App\Models\ModuleApprovement;
use App\Models\Supplier;
use App\Notifications\Supplier\ProductApprovalStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierProductController extends Controller
{
    protected $supplierProductRepository;

    public function __construct(SupplierProductRepositoryInterface $supplierProductRepository)
    {
        $this->supplierProductRepository = $supplierProductRepository;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.supplier-products.index');
    }

    public function data()
    {
        return $this->supplierProductRepository->data();
    }

    public function show($id)
    {
        $product = $this->supplierProductRepository->show($id);
        return view('backend.dashboards.admin.pages.supplier-products.show', compact('product'));
    }

    public function updateApprovalStatus(Request $request, $id)
    {
        $data = $request->validate([
            'action' => 'required|in:pending,under_review,approved,rejected',
            'notes' => 'nullable|string|max:1000'
        ]);

        $data = $this->supplierProductRepository->updateApprovalStatus($data, $id);
        return $data;
    }

    public function supplierProducts($supplierId)
    {
        $data = $this->supplierProductRepository->supplierProducts($supplierId);
        return view('backend.dashboards.admin.pages.supplier-products.supplier-products', $data);
    }
}
