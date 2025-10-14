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
        $governorates = Governorate::all();
        $cities = City::all();
        return view('backend.dashboards.admin.pages.areas.index', compact('cities', 'governorates'));
    }

    public function data()
    {
        return $this->areaRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.admin.pages.areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        return $this->areaRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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
        $area = $this->areaRepo->show($id);

        return view('backend.dashboards.admin.pages.areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, $id)
    {
        return $this->areaRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->areaRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.areas.trash');
    }

    public function trashData()
    {
        return $this->areaRepo->trashData();
    }

    public function restore($id)
    {
        return $this->areaRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->areaRepo->forceDelete($id);
    }
}
