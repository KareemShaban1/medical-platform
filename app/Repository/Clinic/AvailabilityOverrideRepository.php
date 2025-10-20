<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\AvailabilityOverrideRepositoryInterface;
use App\Models\AvailabilityOverride;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\DB;

class AvailabilityOverrideRepository implements AvailabilityOverrideRepositoryInterface
{
    public function index($filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = AvailabilityOverride::query()
            ->whereHas('doctorProfile.clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality']);

        // Apply filters
        if (!empty($filters['doctor_profile_id'])) {
            $query->where('doctor_profile_id', $filters['doctor_profile_id']);
        }

        if (!empty($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('date', 'desc')->orderBy('start_time')->paginate(20);
    }

    public function data($filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = AvailabilityOverride::query()
            ->whereHas('doctorProfile.clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality']);

        // Apply filters
        if (!empty($filters['doctor_profile_id'])) {
            $query->where('doctor_profile_id', $filters['doctor_profile_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        return datatables()->of($query)
            ->addColumn('doctor_name', function ($item) {
                return $item->doctorProfile->name ?? 'N/A';
            })
            ->editColumn('date', function ($item) {
                return $item->date->format('Y-m-d');
            })
            ->editColumn('time_range', function ($item) {
                if ($item->start_time && $item->end_time) {
                    return $item->start_time . ' - ' . $item->end_time;
                }
                return __('All Day');
            })
            ->editColumn('type', function ($item) {
                $badge = $item->type == 'blocked' ? 'danger' : 'success';
                return '<span class="badge bg-' . $badge . '">' . ucfirst($item->type) . '</span>';
            })
            ->addColumn('action', fn($item) => $this->actionButtons($item))
            ->rawColumns(['type', 'action'])
            ->make(true);
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();
            $this->assertDoctorBelongsToClinic($data['doctor_profile_id']);

            $override = AvailabilityOverride::create($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('Availability override created successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();
            $override = $this->find($id);
            $this->assertDoctorBelongsToClinic($override->doctor_profile_id);

            $override->update($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('Availability override updated successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $override = $this->find($id);
            $this->assertDoctorBelongsToClinic($override->doctor_profile_id);

            $override->delete();

            return response()->json([
                'status' => 'success',
                'message' => __('Availability override deleted successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function find($id)
    {
        return AvailabilityOverride::findOrFail($id);
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = AvailabilityOverride::onlyTrashed()
            ->whereHas('doctorProfile.clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality']);

        return datatables()->of($query)
            ->addColumn('doctor_name', function ($item) {
                return $item->doctorProfile->name ?? 'N/A';
            })
            ->editColumn('date', function ($item) {
                return $item->date->format('Y-m-d');
            })
            ->editColumn('time_range', function ($item) {
                if ($item->start_time && $item->end_time) {
                    return $item->start_time . ' - ' . $item->end_time;
                }
                return __('All Day');
            })
            ->editColumn('type', function ($item) {
                $badge = $item->type == 'blocked' ? 'danger' : 'success';
                return '<span class="badge bg-' . $badge . '">' . ucfirst($item->type) . '</span>';
            })
            ->addColumn('trash_action', fn($item) => $this->trashActionButtons($item))
            ->rawColumns(['type', 'trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $override = AvailabilityOverride::onlyTrashed()->findOrFail($id);
        $this->assertDoctorBelongsToClinic($override->doctor_profile_id);
        $override->restore();

        return response()->json([
            'status' => 'success',
            'message' => __('Availability override restored successfully'),
        ]);
    }

    public function forceDelete($id)
    {
        $override = AvailabilityOverride::onlyTrashed()->findOrFail($id);
        $this->assertDoctorBelongsToClinic($override->doctor_profile_id);
        $override->forceDelete();

        return response()->json([
            'status' => 'success',
            'message' => __('Availability override permanently deleted'),
        ]);
    }

    public function forDoctor($doctorProfileId, $filters = [])
    {
        $this->assertDoctorBelongsToClinic($doctorProfileId);

        $query = AvailabilityOverride::where('doctor_profile_id', $doctorProfileId);

        if (!empty($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->orderBy('date')->orderBy('start_time')->get();
    }

    public function forDateRange($doctorProfileId, $startDate, $endDate)
    {
        $this->assertDoctorBelongsToClinic($doctorProfileId);

        return AvailabilityOverride::where('doctor_profile_id', $doctorProfileId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    private function actionButtons($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editOverride({$item->id})" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="deleteOverride({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function trashActionButtons($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
            <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function assertDoctorBelongsToClinic($doctorProfileId): void
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $belongs = DoctorProfile::where('id', $doctorProfileId)
            ->whereHas('clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->exists();

        if (!$belongs) {
            throw new \Exception(__('Unauthorized action'));
        }
    }
}

