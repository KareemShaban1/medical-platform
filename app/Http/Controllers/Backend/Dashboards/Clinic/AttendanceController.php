<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\AttendanceRepositoryInterface;
use App\Models\AttendanceLog;
use App\Services\Attendance\HoursCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $repo;
    protected $calculator;

    public function __construct(AttendanceRepositoryInterface $repo, HoursCalculator $calculator)
    {
        $this->repo = $repo;
        $this->calculator = $calculator;
    }

    public function index()
    {
        $today = now()->toDateString();
        $logs = $this->repo->listForDay($today);
        $pending = $this->repo->pendingAbsences();
        $clinicUsers = \App\Models\ClinicUser::where('clinic_id', auth('clinic')->user()->clinic_id)->orderBy('name')->get();
        return view('backend.dashboards.clinic.pages.attendance.index', compact('logs', 'pending', 'today', 'clinicUsers'));
    }

    public function checkIn(Request $request)
    {
        $request->validate(['at' => 'nullable|date']);
        $at = $request->input('at', now());
        try {
            $log = $this->repo->checkIn(auth('clinic')->id(), $at, 'web', $request->input('notes'));
            return response()->json(['status' => 'success', 'message' => __('Checked in'), 'data' => $log]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function checkOut(Request $request)
    {
        $request->validate(['at' => 'nullable|date']);
        $at = $request->input('at', now());
        try {
            $log = $this->repo->checkOut(auth('clinic')->id(), $at, 'web', $request->input('notes'));
            return response()->json(['status' => 'success', 'message' => __('Checked out'), 'data' => $log]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function absence(Request $request)
    {
        $request->validate([
            'at' => 'required|date',
            'notes' => 'nullable|string',
            'attachments' => 'nullable',
            'attachments.*' => 'file|max:10240',
        ]);
        $log = $this->repo->requestAbsence($request->user('clinic')->id, $request->at, $request->notes);
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file) {
                    $log->addMedia($file)->toMediaCollection('absence_attachments');
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => __('Absence requested'), 'data' => $log]);
    }

    public function attachments($id)
    {
        $log = AttendanceLog::with(['clinicUser'])->findOrFail($id);
        if (!$log->clinicUser || $log->clinicUser->clinic_id !== auth('clinic')->user()->clinic_id) {
            return response()->json(['status' => 'error', 'message' => __('Unauthorized')], 403);
        }
        $items = $log->getMedia('absence_attachments')->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->file_name,
                'mime' => $m->mime_type,
                'size' => $m->size,
                'url' => $m->getFullUrl(),
                'preview_url' => $m->hasGeneratedConversion('thumb') ? $m->getFullUrl('thumb') : $m->getFullUrl(),
            ];
        });
        return response()->json(['status' => 'success', 'data' => $items]);
    }

    public function approve($id)
    {
        try {
            $log = $this->repo->approve($id, auth('clinic')->id());
            return response()->json(['status' => 'success', 'message' => __('Attendance approved'), 'data' => $log]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function approveCheckIn($id)
    {
        try {
            $log = $this->repo->approveCheckIn($id, auth('clinic')->id());
            return response()->json(['status' => 'success', 'message' => __('Check-in approved'), 'data' => $log]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function approveCheckOut($id)
    {
        try {
            $log = $this->repo->approveCheckOut($id, auth('clinic')->id());
            return response()->json(['status' => 'success', 'message' => __('Check-out approved'), 'data' => $log]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function myLogs(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);
        $start = $request->input('start', now()->copy()->subDays(30)->toDateString());
        $end = $request->input('end', now()->toDateString());
        $logs = $this->repo->listForUser(auth('clinic')->id(), $start, $end);
        return response()->json(['status' => 'success', 'data' => $logs]);
    }

    public function compute(Request $request)
    {
        $request->validate([
            'clinic_user_id' => 'required|exists:clinic_users,id',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);
        $start = Carbon::parse($request->start)->startOfDay()->toDateTimeString();
        $end = Carbon::parse($request->end)->endOfDay()->toDateTimeString();
        $result = $this->calculator->compute((int)$request->clinic_user_id, $start, $end);
        return response()->json(['status' => 'success', 'data' => $result]);
    }
}
