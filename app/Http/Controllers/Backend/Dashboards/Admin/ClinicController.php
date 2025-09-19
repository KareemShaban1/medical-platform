<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Repository\Admin\ClinicRepositoryInterface;
use App\Http\Requests\Admin\Store\StoreClinicRequest;
use App\Http\Requests\Admin\Update\UpdateClinicRequest;
use Illuminate\Http\Request;

class ClinicController extends Controller
{

    
    protected $clinicRepo;

    public function __construct(ClinicRepositoryInterface $clinicRepo)
    {
        $this->clinicRepo = $clinicRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.clinics.index');
    }

    public function data()
    {
        return $this->clinicRepo->data();
    }

    public function store(StoreClinicRequest $request)
    {
        return $this->clinicRepo->store($request);
    }

    public function show($id)
    {
        $clinic = $this->clinicRepo->show($id);

        return request()->ajax()
            ? response()->json($clinic)
            : view('backend.dashboards.admin.pages.clinics.show', compact('clinic'));
    }

    public function update(UpdateClinicRequest $request, $id)
    {
        return $this->clinicRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->clinicRepo->updateStatus($request);
    }

    public function destroy($id)
    {
        return $this->clinicRepo->destroy($id);
    }

   
}
