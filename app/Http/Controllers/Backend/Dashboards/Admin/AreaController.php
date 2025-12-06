<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Area\StoreAreaRequest;
use App\Http\Requests\Admin\Area\UpdateAreaRequest;
use App\Interfaces\Admin\AreaRepositoryInterface;
use App\Models\City;
use App\Models\Governorate;

class AreaController extends Controller
{
     protected $areaRepo;

    public function __construct(AreaRepositoryInterface $areaRepo)
    {
        $this->areaRepo = $areaRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view areas'), 403, __('You are not authorized to view areas'));
        $governorates = Governorate::all();
        $cities = City::all();
        return view('backend.dashboards.admin.pages.areas.index', compact('cities', 'governorates'));
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view areas'), 403, __('You are not authorized to view areas'));
        return $this->areaRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create area'), 403, __('You are not authorized to create area'));
        return view('backend.dashboards.admin.pages.areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create area'), 403, __('You are not authorized to create area'));
        return $this->areaRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view areas'), 403, __('You are not authorized to view area'));
        $area = $this->areaRepo->show($id);

        return request()->ajax()
            ? response()->json($area)
            : view('backend.dashboards.admin.pages.areas.show', compact('area'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update area'), 403, __('You are not authorized to update area'));
        $area = $this->areaRepo->show($id);

        return view('backend.dashboards.admin.pages.areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update area'), 403, __('You are not authorized to update area'));
        return $this->areaRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete area'), 403, __('You are not authorized to delete area'));
        return $this->areaRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash areas'), 403, __('You are not authorized to view trash areas'));
        return view('backend.dashboards.admin.pages.areas.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash areas'), 403, __('You are not authorized to view trash areas'));
        return $this->areaRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore area'), 403, __('You are not authorized to restore area'));
        return $this->areaRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete area'), 403, __('You are not authorized to force delete area'));
        return $this->areaRepo->forceDelete($id);
    }
}