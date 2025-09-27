<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\SupplierRepositoryInterface;
use App\Http\Requests\Admin\Store\StoreSupplierRequest;
use App\Http\Requests\Admin\Update\UpdateSupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{


    protected $supplierRepo;

    public function __construct(SupplierRepositoryInterface $supplierRepo)
    {
        $this->supplierRepo = $supplierRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.suppliers.index');
    }

    public function data()
    {
        return $this->supplierRepo->data();
    }

    public function store(StoreSupplierRequest $request)
    {
        return $this->supplierRepo->store($request);
    }

    public function show($id)
    {
        $supplier = $this->supplierRepo->show($id);

        return request()->ajax()
            ? response()->json($supplier)
            : view('backend.dashboards.admin.pages.suppliers.show', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, $id)
    {
        return $this->supplierRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->supplierRepo->updateStatus($request);
    }

    public function updateIsAllowed(Request $request)
    {
        return $this->supplierRepo->updateIsAllowed($request);
    }

    public function destroy($id)
    {
        return $this->supplierRepo->destroy($id);
    }

    public function showApproval($id)
    {
        return $this->supplierRepo->showApproval($id);
    }
}
