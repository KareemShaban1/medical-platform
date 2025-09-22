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
        return view('backend.dashboards.admin.pages.rental-spaces.index');
    }

    public function create()
    {
        return view('backend.dashboards.clinic.pages.rental-spaces.create');
    }


    public function data()
    {
        return $this->rentalSpaceRepositoryInterface->data();
    }

    public function store(StoreRentalSpaceRequest $request)
    {
        return $this->rentalSpaceRepositoryInterface->store($request);
    }

    public function show($id)
    {
        $rentalSpace = $this->rentalSpaceRepositoryInterface->show($id);

        return request()->ajax()
            ? response()->json($rentalSpace)
            : view('backend.dashboards.admin.pages.rental-spaces.show', compact('rentalSpace'));
    }

    public function edit($id)
    {
        $rentalSpace = $this->rentalSpaceRepositoryInterface->show($id);

        return view('backend.dashboards.admin.pages.rental-spaces.edit', compact('rentalSpace'));
    }

    public function update(UpdateRentalSpaceRequest $request, $id)
    {
        return $this->rentalSpaceRepositoryInterface->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->rentalSpaceRepositoryInterface->updateStatus($request);
    }

    public function destroy($id)
    {
        return $this->rentalSpaceRepositoryInterface->destroy($id);
    }

    
}