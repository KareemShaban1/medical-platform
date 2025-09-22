<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\ClinicRepositoryInterface;
use App\Http\Requests\Admin\Store\StoreClinicRequest;
use App\Http\Requests\Admin\Update\UpdateClinicRequest;
use App\Models\Clinic;
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

    public function clinicUsersData($id)
    {
        return $this->clinicRepo->clinicUsersData($id);
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

    public function updateIsAllowed(Request $request)
    {
        return $this->clinicRepo->updateIsAllowed($request);
    }

    public function toggleStatus($id)
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->update(['status' => !$clinic->status]);

        return $this->jsonResponse('success', __('Clinic status updated successfully'));
    }

    public function destroy($id)
    {
        return $this->clinicRepo->destroy($id);
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message ], $status === 'error' ? 400 : 200);
        }

        return redirect()->back()->with($status, $message);
    }


}
