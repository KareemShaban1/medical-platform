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
        return view('backend.dashboards.clinic.pages.rental-spaces.index');
    }

    public function data()
    {
        return $this->rentalSpaceRepo->data();
    }

    // create 
    public function create()
    {
        return view('backend.dashboards.clinic.pages.rental-spaces.create');
    }

    public function store(StoreRentalSpaceRequest $request)
    {
        return $this->rentalSpaceRepo->store($request);
    }

    public function show($id)
    {
        $rentalSpace = $this->rentalSpaceRepo->show($id);

        return request()->ajax()
            ? response()->json($rentalSpace)
            : view('backend.dashboards.clinic.pages.rental-spaces.show', compact('rentalSpace'));
    }

    public function edit($id){
        $rentalSpace = $this->rentalSpaceRepo->show($id);

        return view('backend.dashboards.clinic.pages.rental-spaces.edit', compact('rentalSpace'));

    }

    public function update(UpdateRentalSpaceRequest $request, $id)
    {
        return $this->rentalSpaceRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->rentalSpaceRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        return $this->rentalSpaceRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.rental-spaces.trash');
    }

    public function trashData()
    {
        return $this->rentalSpaceRepo->trashData();
    }
    

    public function restore($id)
    {
        return $this->rentalSpaceRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->rentalSpaceRepo->forceDelete($id);
    }

    
}