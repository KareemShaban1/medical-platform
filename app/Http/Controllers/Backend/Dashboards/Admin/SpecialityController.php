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
        return view('backend.dashboards.admin.pages.specialities.index');
    }

    public function data()
    {
        return $this->repo->data();
    }

    public function store(StoreSpecialityRequest $request)
    {
        return $this->repo->store($request);
    }

    public function show($id)
    {
        $item = $this->repo->show($id);
        return request()->ajax() ? response()->json($item) : view('backend.dashboards.admin.pages.specialities.show', compact('item'));
    }

    public function update(UpdateSpecialityRequest $request, $id)
    {
        return $this->repo->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->repo->destroy($id);
    }
}
