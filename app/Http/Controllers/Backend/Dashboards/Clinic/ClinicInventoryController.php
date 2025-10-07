<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Models\ClinicInventory;
use App\Http\Requests\Clinic\Inventory\StoreClinicInventoryRequest;
use App\Http\Requests\Clinic\Inventory\UpdateClinicInventoryRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\ClinicInventoryRepositoryInterface;



class ClinicInventoryController extends Controller
{
    protected $clinicInventoryRepo;

    public function __construct(ClinicInventoryRepositoryInterface $clinicInventoryRepo)
    {
        $this->clinicInventoryRepo = $clinicInventoryRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.dashboards.clinic.pages.clinic-inventories.index');
    }

    public function data()
    {
                return $this->clinicInventoryRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.clinic.pages.clinic-inventories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClinicInventoryRequest $request)
    {
        return $this->clinicInventoryRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $clinicInventory = $this->clinicInventoryRepo->show($id);

        return request()->ajax()
            ? response()->json($clinicInventory)
            : view('backend.dashboards.clinic.pages.clinic-inventories.show', compact('clinicInventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $clinicInventory = $this->clinicInventoryRepo->show($id);

        return view('backend.dashboards.clinic.pages.clinic-inventories.edit', compact('clinicInventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicInventoryRequest $request, $id)
    {
        return $this->clinicInventoryRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->clinicInventoryRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.clinic-inventories.trash');
    }

    public function trashData()
    {
            return $this->clinicInventoryRepo->trashData();
    }

    public function restore($id)
    {
        return $this->clinicInventoryRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->clinicInventoryRepo->forceDelete($id);
    }
}