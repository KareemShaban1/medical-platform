<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\City\StoreCityRequest;
use App\Http\Requests\Admin\City\UpdateCityRequest;
use App\Interfaces\Admin\CityRepositoryInterface;
use App\Models\Governorate;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cityRepo;

    public function __construct(CityRepositoryInterface $cityRepo)
    {
        $this->cityRepo = $cityRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $governorates = Governorate::all();
        return view('backend.dashboards.admin.pages.cities.index', compact('governorates'));
    }

    public function data()
    {
        return $this->cityRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.admin.pages.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        return $this->cityRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $city = $this->cityRepo->show($id);

        return request()->ajax()
            ? response()->json($city)
            : view('backend.dashboards.admin.pages.cities.show', compact('city'));
    }

    public function getCitiesByGovernorateId(Request $request)
    {
        return $this->cityRepo->getCitiesByGovernorateId($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $city = $this->cityRepo->show($id);

        return view('backend.dashboards.admin.pages.cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, $id)
    {
        return $this->cityRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->cityRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.cities.trash');
    }

    public function trashData()
    {
        return $this->cityRepo->trashData();
    }

    public function restore($id)
    {
        return $this->cityRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->cityRepo->forceDelete($id);
    }
}
