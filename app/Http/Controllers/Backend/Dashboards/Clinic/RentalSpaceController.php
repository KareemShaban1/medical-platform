<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Clinic\RentalSpace\StoreRentalSpaceRequest;
use App\Http\Requests\Clinic\RentalSpace\UpdateRentalSpaceRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Interfaces\Clinic\RentalSpaceRepositoryInterface;

class RentalSpaceController extends Controller
{
    protected $rentalSpaceRepo;

    public function __construct(RentalSpaceRepositoryInterface $rentalSpaceRepo)
    {
        $this->rentalSpaceRepo = $rentalSpaceRepo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view rental spaces'), 403, __('You are not authorized to view rental spaces'));

        return view('backend.dashboards.clinic.pages.rental-spaces.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view rental spaces'), 403, __('You are not authorized to view rental spaces'));

        return $this->rentalSpaceRepo->data();
    }

    // create
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create rental space'), 403, __('You are not authorized to create rental space'));

        return view('backend.dashboards.clinic.pages.rental-spaces.create');
    }

    public function store(StoreRentalSpaceRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create rental space'), 403, __('You are not authorized to create rental space'));

        return $this->rentalSpaceRepo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view rental space'), 403, __('You are not authorized to view rental space'));

        $rentalSpace = $this->rentalSpaceRepo->show($id);

        return request()->ajax()
            ? response()->json($rentalSpace)
            : view('backend.dashboards.clinic.pages.rental-spaces.show', compact('rentalSpace'));
    }

    public function edit($id){
        // apply permissions
        abort_if(!hasPermission('update rental space'), 403, __('You are not authorized to update rental space'));

        $rentalSpace = $this->rentalSpaceRepo->show($id);

        return view('backend.dashboards.clinic.pages.rental-spaces.edit', compact('rentalSpace'));

    }

    public function update(UpdateRentalSpaceRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update rental space'), 403, __('You are not authorized to update rental space'));

        return $this->rentalSpaceRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->rentalSpaceRepo->updateStatus($request);
    }

    public function destroy($id)
    {
               // apply permissions
               abort_if(!hasPermission('delete rental space'), 403, __('You are not authorized to delete rental space'));

        return $this->rentalSpaceRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash rental spaces'), 403, __('You are not authorized to view trash rental spaces'));

        return view('backend.dashboards.clinic.pages.rental-spaces.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash rental spaces'), 403, __('You are not authorized to view trash rental spaces'));

        return $this->rentalSpaceRepo->trashData();
    }


    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore rental space'), 403, __('You are not authorized to restore rental space'));

        return $this->rentalSpaceRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete rental space'), 403, __('You are not authorized to force delete rental space'));

        return $this->rentalSpaceRepo->forceDelete($id);
    }


}
