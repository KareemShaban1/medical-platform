<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\RentalSpace\StoreRentalSpaceRequest;
use App\Http\Requests\Clinic\RentalSpace\UpdateRentalSpaceRequest;
use Illuminate\Http\Request;
use App\Interfaces\Admin\RentalSpaceRepositoryInterface;

class RentalSpaceController extends Controller
{
    protected $rentalSpaceRepositoryInterface;

    public function __construct(RentalSpaceRepositoryInterface $rentalSpaceRepositoryInterface)
    {
        $this->rentalSpaceRepositoryInterface = $rentalSpaceRepositoryInterface;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view rental spaces'), 403, __('You are not authorized to view rental spaces'));
        return view('backend.dashboards.admin.pages.rental-spaces.index');
    }

    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create rental space'), 403, __('You are not authorized to create rental space'));
        return view('backend.dashboards.clinic.pages.rental-spaces.create');
    }


    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view rental spaces'), 403, __('You are not authorized to view rental spaces'));
        return $this->rentalSpaceRepositoryInterface->data();
    }

    public function store(StoreRentalSpaceRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create rental space'), 403, __('You are not authorized to create rental space'));
        return $this->rentalSpaceRepositoryInterface->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view rental space'), 403, __('You are not authorized to view rental space'));
        $rentalSpace = $this->rentalSpaceRepositoryInterface->show($id);

        return request()->ajax()
            ? response()->json($rentalSpace)
            : view('backend.dashboards.admin.pages.rental-spaces.show', compact('rentalSpace'));
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update rental space'), 403, __('You are not authorized to update rental space'));
        $rentalSpace = $this->rentalSpaceRepositoryInterface->show($id);

        return view('backend.dashboards.admin.pages.rental-spaces.edit', compact('rentalSpace'));
    }

    public function update(UpdateRentalSpaceRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update rental space'), 403, __('You are not authorized to update rental space'));
        return $this->rentalSpaceRepositoryInterface->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('toggle rental space status'), 403, __('You are not authorized to update rental space status'));
        return $this->rentalSpaceRepositoryInterface->updateStatus($request);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete rental space'), 403, __('You are not authorized to delete rental space'));
        return $this->rentalSpaceRepositoryInterface->destroy($id);
    }


}
