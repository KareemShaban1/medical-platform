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
        // apply permissions
        abort_if(!hasPermission('view suppliers'), 403, __('You are not authorized to view suppliers'));
        return view('backend.dashboards.admin.pages.suppliers.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view suppliers'), 403, __('You are not authorized to view suppliers'));
        return $this->supplierRepo->data();
    }

    public function store(StoreSupplierRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create supplier'), 403, __('You are not authorized to create suppliers'));
        return $this->supplierRepo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view supplier'), 403, __('You are not authorized to view supplier'));
        $supplier = $this->supplierRepo->show($id);

        return request()->ajax()
            ? response()->json($supplier)
            : view('backend.dashboards.admin.pages.suppliers.show', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update supplier'), 403, __('You are not authorized to update supplier'));
        return $this->supplierRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('toggle supplier status'), 403, __('You are not authorized to update supplier status'));
        return $this->supplierRepo->updateStatus($request);
    }

    public function updateIsAllowed(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('toggle supplier allowed'), 403, __('You are not authorized to update supplier allowed'));
        return $this->supplierRepo->updateIsAllowed($request);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete supplier'), 403, __('You are not authorized to delete supplier'));
        return $this->supplierRepo->destroy($id);
    }

    public function showApproval($id)
    {
        // apply permissions
        abort_if(!hasPermission('approve supplier'), 403, __('You are not authorized to view supplier approval'));
        return $this->supplierRepo->showApproval($id);
    }
}
