<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\DailyPeriodRepositoryInterface;
use App\Models\DailyPeriod;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\DB;

class DailyPeriodRepository implements DailyPeriodRepositoryInterface
{
    public function index($filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = DailyPeriod::query()
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

        if (isset($filters['is_open'])) {
            $query->where('is_open', $filters['is_open']);
        }

        if (!empty($filters['capacity_status'])) {
            switch ($filters['capacity_status']) {
                case 'available':
                    $query->whereRaw('booked_count < capacity');
                    break;
                case 'full':
                    $query->whereRaw('booked_count >= capacity');
                    break;
            }
        }

        return $query->orderBy('date', 'desc')->orderBy('start_time')->paginate(20);
    }

    public function data($filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = DailyPeriod::query()
            ->whereHas('doctorProfile.clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->leftJoin('doctor_profiles', 'daily_periods.doctor_profile_id', '=', 'doctor_profiles.id')
            ->select('daily_periods.*')
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality'])
            ->distinct();

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

        if (isset($filters['is_open'])) {
            $query->where('is_open', $filters['is_open']);
        }

        if (!empty($filters['capacity_status'])) {
            switch ($filters['capacity_status']) {
                case 'available':
                    $query->whereRaw('booked_count < capacity');
                    break;
                case 'full':
                    $query->whereRaw('booked_count >= capacity');
                    break;
            }
        }

        return datatables()->of($query)
            ->filterColumn('doctor_name', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('doctor_profiles.name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('time', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('daily_periods.start_time', 'like', "%{$keyword}%")
                      ->orWhere('daily_periods.end_time', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('date', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->whereRaw("DATE_FORMAT(daily_periods.date, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"])
                      ->orWhereRaw("DATE_FORMAT(daily_periods.date, '%d/%m/%Y') LIKE ?", ["%{$keyword}%"]);
                });
            })
            ->addColumn('doctor_name', function ($item) {
                return $item->doctorProfile->name ?? 'N/A';
            })
            ->editColumn('date', function ($item) {
                return $item->date->format('Y-m-d');
            })
            ->addColumn('time', function ($item) {
                return $item->start_time . ' - ' . $item->end_time;
            })
            ->addColumn('capacity_display', function ($item) {
                $badge = $item->is_full ? 'danger' : 'success';
                return '<span class="badge bg-' . $badge . '">' . $item->booked_count . '/' . $item->capacity . '</span>';
            })
            ->editColumn('is_open', function ($item) {
                $badge = $item->is_open ? 'success' : 'secondary';
                $text = $item->is_open ? __('Open') : __('Closed');
                return '<span class="badge bg-' . $badge . '">' . $text . '</span>';
            })
            ->addColumn('action', fn($item) => $this->actionButtons($item))
            ->rawColumns(['capacity_display', 'is_open', 'action'])
            ->make(true);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $this->assertDoctorBelongsToClinic($data['doctor_profile_id']);

            return DailyPeriod::create($data);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $period = $this->find($id);
            $this->assertDoctorBelongsToClinic($period->doctor_profile_id);

            $period->update($data);
            return $period->fresh();
        });
    }

    public function destroy($id)
    {
        $period = $this->find($id);
        $this->assertDoctorBelongsToClinic($period->doctor_profile_id);

        return $period->delete();
    }

    public function find($id)
    {
        return DailyPeriod::findOrFail($id);
    }

    public function forDoctor($doctorProfileId, $filters = [])
    {
        $this->assertDoctorBelongsToClinic($doctorProfileId);

        $query = DailyPeriod::where('doctor_profile_id', $doctorProfileId);

        if (!empty($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['is_open'])) {
            $query->where('is_open', $filters['is_open']);
        }

        return $query->orderBy('date')->orderBy('start_time')->get();
    }

    public function forDateRange($doctorProfileId, $startDate, $endDate)
    {
        $this->assertDoctorBelongsToClinic($doctorProfileId);

        return DailyPeriod::where('doctor_profile_id', $doctorProfileId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    public function toggleOpen($id)
    {
        return DB::transaction(function () use ($id) {
            $period = $this->find($id);
            $this->assertDoctorBelongsToClinic($period->doctor_profile_id);

            $period->update(['is_open' => !$period->is_open]);
            return $period->fresh();
        });
    }

    public function updateCapacity($id, $capacity)
    {
        return DB::transaction(function () use ($id, $capacity) {
            $period = $this->find($id);
            $this->assertDoctorBelongsToClinic($period->doctor_profile_id);

            $period->update(['capacity' => $capacity]);
            return $period->fresh();
        });
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

    private function actionButtons($item): string
    {
        $toggleIcon = $item->is_open ? 'lock' : 'lock-open';
        $toggleClass = $item->is_open ? 'warning' : 'success';
        $toggleTitle = $item->is_open ? __('Close') : __('Open');

        $html = '<div class="d-flex gap-2">';

        if (hasPermission('view daily period appointments')) {
            $html .= '<button onclick="viewAppointments(' . $item->id . ')" class="btn btn-sm btn-info text-white" title="View Appointments">';
            $html .= '<i class="mdi mdi-calendar-check"></i>';
            $html .= '</button>';
        }

        if (hasPermission('update daily period capacity')) {
            $html .= '<button onclick="editCapacity(' . $item->id . ', ' . $item->capacity . ')" class="btn btn-sm btn-primary" title="Edit Capacity">';
            $html .= '<i class="mdi mdi-pencil"></i>';
            $html .= '</button>';
        }

        if (hasPermission('toggle daily period open')) {
            $html .= '<button onclick="toggleStatus(' . $item->id . ')" class="btn btn-sm btn-' . $toggleClass . '" title="' . $toggleTitle . '">';
            $html .= '<i class="mdi mdi-' . $toggleIcon . '"></i>';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }
}
