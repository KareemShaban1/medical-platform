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
        // apply permissions
        abort_if(!hasPermission('view governorates'), 403, __('You are not authorized to view governorates'));
        return view('backend.dashboards.admin.pages.governorates.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view governorates'), 403, __('You are not authorized to view governorates'));
        return $this->governorateRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create governorate'), 403, __('You are not authorized to create governorate'));
        return view('backend.dashboards.admin.pages.governorates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGovernorateRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create governorate'), 403, __('You are not authorized to create governorate'));
        return $this->governorateRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view governorates'), 403, __('You are not authorized to view governorate'));
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
        // apply permissions
        abort_if(!hasPermission('update governorate'), 403, __('You are not authorized to update governorate'));
        $governorate = $this->governorateRepo->show($id);

        return view('backend.dashboards.admin.pages.governorates.edit', compact('governorate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGovernorateRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update governorate'), 403, __('You are not authorized to update governorate'));
        return $this->governorateRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete governorate'), 403, __('You are not authorized to delete governorate'));
        return $this->governorateRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash governorates'), 403, __('You are not authorized to view trash governorates'));
        return view('backend.dashboards.admin.pages.governorates.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash governorates'), 403, __('You are not authorized to view trash governorates'));
        return $this->governorateRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore governorate'), 403, __('You are not authorized to restore governorate'));
        return $this->governorateRepo->restore($id);
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete governorate'), 403, __('You are not authorized to force delete governorate'));
        return $this->governorateRepo->forceDelete($id);
    }
}