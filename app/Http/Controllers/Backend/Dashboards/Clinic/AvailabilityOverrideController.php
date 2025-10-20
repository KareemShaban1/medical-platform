<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\AvailabilityOverride\StoreAvailabilityOverrideRequest;
use App\Http\Requests\Clinic\AvailabilityOverride\UpdateAvailabilityOverrideRequest;
use App\Interfaces\Clinic\AvailabilityOverrideRepositoryInterface;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class AvailabilityOverrideController extends Controller
{
    protected $repo;

    public function __construct(AvailabilityOverrideRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $clinicId = auth('clinic')->user()->clinic_id;
        $doctors = DoctorProfile::whereHas('clinicUser', function ($q) use ($clinicId) {
            $q->where('clinic_id', $clinicId);
        })->where('status', DoctorProfile::STATUS_APPROVED)->get();

        return view('backend.dashboards.clinic.pages.availability-overrides.index', compact('doctors'));
    }

    public function data(Request $request)
    {
        return $this->repo->data($request->all());
    }

    public function store(StoreAvailabilityOverrideRequest $request)
    {
        return $this->repo->store($request->validated());
    }

    public function show($id)
    {
        $override = $this->repo->find($id);

        return request()->ajax()
            ? response()->json($override)
            : view('backend.dashboards.clinic.pages.availability-overrides.show', compact('override'));
    }

    public function update(UpdateAvailabilityOverrideRequest $request, $id)
    {
        return $this->repo->update($id, $request->validated());
    }

    public function destroy($id)
    {
        return $this->repo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.availability-overrides.trash');
    }

    public function trashData()
    {
        return $this->repo->trashData();
    }

    public function restore($id)
    {
        return $this->repo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->repo->forceDelete($id);
    }

    public function forDoctor($doctorId, Request $request)
    {
        try {
            $filters = $request->only(['date', 'start_date', 'end_date']);
            $overrides = $this->repo->forDoctor($doctorId, $filters);
            return response()->json($overrides);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}

