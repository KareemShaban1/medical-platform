<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\WorkingHour\BulkSaveWorkingHoursRequest;
use App\Interfaces\Clinic\WorkingHourRepositoryInterface;
use App\Models\ClinicUser;

class WorkingHourController extends Controller
{
    protected $repo;

    public function __construct(WorkingHourRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $clinicUsers = ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)
            ->orderBy('name')
            ->get();

        return view('backend.dashboards.clinic.pages.working-hours.index', compact('clinicUsers'));
    }

    public function forUser($clinicUserId)
    {
        $hours = $this->repo->forUser($clinicUserId);
        return response()->json($hours);
    }

    public function bulkSave(BulkSaveWorkingHoursRequest $request)
    {
        $validated = $request->validated();
        $slots = $validated['slots'] ?? [];
        $isRecurring = (bool)($validated['is_recurring'] ?? true);
        $data = $this->repo->bulkSave($validated['clinic_user_id'], $slots, $isRecurring);
        return response()->json(['status' => 'success', 'message' => __('Working hours saved successfully'), 'data' => $data]);
    }

    public function destroy($id)
    {
        $this->repo->destroy($id);
        return response()->json(['status' => 'success', 'message' => __('Slot deleted successfully')]);
    }
}

