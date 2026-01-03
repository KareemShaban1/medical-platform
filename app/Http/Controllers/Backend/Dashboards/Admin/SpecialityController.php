<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Repository\Admin\SpecialityRepository;
use App\Http\Requests\Admin\Speciality\StoreSpecialityRequest;
use App\Http\Requests\Admin\Speciality\UpdateSpecialityRequest;

class SpecialityController extends Controller
{
    protected $repo;

    public function __construct(SpecialityRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view specialities'), 403, __('You are not authorized to view specialities'));
        return view('backend.dashboards.admin.pages.specialities.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view specialities'), 403, __('You are not authorized to view specialities'));
        return $this->repo->data();
    }

    public function store(StoreSpecialityRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create speciality'), 403, __('You are not authorized to create specialities'));
        return $this->repo->store($request);
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view specialities'), 403, __('You are not authorized to view specialities'));
        $item = $this->repo->show($id);
        return request()->ajax() ? response()->json($item) : view('backend.dashboards.admin.pages.specialities.show', compact('item'));
    }

    public function update(UpdateSpecialityRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update speciality'), 403, __('You are not authorized to update specialities'));
        return $this->repo->update($request, $id);
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete speciality'), 403, __('You are not authorized to delete specialities'));
        return $this->repo->destroy($id);
    }

    public function trash()
    {
        abort_if(!hasPermission('view trash specialities'), 403, __('You are not authorized to view trash'));
        return view('backend.dashboards.admin.pages.specialities.trash');
    }

    public function trashData()
    {
        abort_if(!hasPermission('view trash specialities'), 403, __('You are not authorized to view trash'));
        return $this->repo->trashData();
    }

    public function restore($id)
    {
        abort_if(!hasPermission('restore speciality'), 403, __('You are not authorized to restore'));
        return $this->repo->restore($id);
    }

    public function forceDelete($id)
    {
        abort_if(!hasPermission('force delete speciality'), 403, __('You are not authorized to force delete'));
        return $this->repo->forceDelete($id);
    }
}
