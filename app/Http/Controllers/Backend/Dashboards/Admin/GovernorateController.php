<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Governorate\StoreGovernorateRequest;
use App\Http\Requests\Admin\Governorate\UpdateGovernorateRequest;
use App\Interfaces\Admin\GovernorateRepositoryInterface;

class GovernorateController extends Controller
{
    protected $governorateRepo;

    public function __construct(GovernorateRepositoryInterface $governorateRepo)
    {
        $this->governorateRepo = $governorateRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.dashboards.admin.pages.governorates.index');
    }

    public function data()
    {
        return $this->governorateRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.admin.pages.governorates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGovernorateRequest $request)
    {
        return $this->governorateRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $governorate = $this->governorateRepo->show($id);

        return request()->ajax()
            ? response()->json($governorate)
            : view('backend.dashboards.admin.pages.governorates.show', compact('governorate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $governorate = $this->governorateRepo->show($id);

        return view('backend.dashboards.admin.pages.governorates.edit', compact('governorate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGovernorateRequest $request, $id)
    {
        return $this->governorateRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->governorateRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.governorates.trash');
    }

    public function trashData()
    {
        return $this->governorateRepo->trashData();
    }

    public function restore($id)
    {
        return $this->governorateRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->governorateRepo->forceDelete($id);
    }
}
