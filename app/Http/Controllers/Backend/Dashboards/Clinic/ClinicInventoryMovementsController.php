<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Models\ClinicInventory;
use App\Http\Requests\Clinic\Inventory\StoreClinicInventoryMovementRequest;
use App\Http\Requests\Clinic\Inventory\UpdateClinicInventoryMovementRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\ClinicInventoryMovementRepositoryInterface;



class ClinicInventoryMovementsController extends Controller
{
    protected $clinicInventoryMovementRepo;

    public function __construct(ClinicInventoryMovementRepositoryInterface $clinicInventoryMovementRepo)
    {
        $this->clinicInventoryMovementRepo = $clinicInventoryMovementRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        return view('backend.dashboards.clinic.pages.clinic-inventory-movements.index', compact('id'));
    }

    public function data($id)
    {
                return $this->clinicInventoryMovementRepo->data($id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('backend.dashboards.clinic.pages.clinic-inventory-movements.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClinicInventoryMovementRequest $request)
    {
        return $this->clinicInventoryMovementRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $clinicInventoryMovement = $this->clinicInventoryMovementRepo->show($id);

        return request()->ajax()
            ? response()->json($clinicInventoryMovement)
            : view('backend.dashboards.clinic.pages.clinic-inventory-movements.show', compact('clinicInventoryMovement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $clinicInventoryMovement = $this->clinicInventoryMovementRepo->show($id);

        return view('backend.dashboards.clinic.pages.clinic-inventory-movements.edit', compact('clinicInventoryMovement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicInventoryMovementRequest $request, $id)
    {
        return $this->clinicInventoryMovementRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->clinicInventoryMovementRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.clinic-inventory-movements.trash');
    }

    public function trashData()
    {
            return $this->clinicInventoryMovementRepo->trashData();
    }

    public function restore($id)
    {
        return $this->clinicInventoryMovementRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->clinicInventoryMovementRepo->forceDelete($id);
    }
}