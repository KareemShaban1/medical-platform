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
        // apply permissions
        abort_if(!hasPermission('view cities'), 403, __('You are not authorized to view cities'));
        $governorates = Governorate::all();
        return view('backend.dashboards.admin.pages.cities.index', compact('governorates'));
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view cities'), 403, __('You are not authorized to view cities'));
        return $this->cityRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create city'), 403, __('You are not authorized to create city'));
        return view('backend.dashboards.admin.pages.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create city'), 403, __('You are not authorized to create city'));
        return $this->cityRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view cities'), 403, __('You are not authorized to view city'));
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
        // apply permissions
        abort_if(!hasPermission('update city'), 403, __('You are not authorized to update city'));
        $city = $this->cityRepo->show($id);

        return view('backend.dashboards.admin.pages.cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update city'), 403, __('You are not authorized to update city'));
        return $this->cityRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete city'), 403, __('You are not authorized to delete city'));
        return $this->cityRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash cities'), 403, __('You are not authorized to view trash cities'));
        return view('backend.dashboards.admin.pages.cities.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash cities'), 403, __('You are not authorized to view trash cities'));
        return $this->cityRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore city'), 403, __('You are not authorized to restore city'));
        return $this->cityRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete city'), 403, __('You are not authorized to force delete city'));
        return $this->cityRepo->forceDelete($id);
    }
}
