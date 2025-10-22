<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\AppointmentRepositoryInterface;
use App\Models\Appointment;
use App\Models\DailyPeriod;
use App\Models\DoctorProfile;
use Illuminate\Support\Facades\DB;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function index($filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = Appointment::query()
            ->whereHas('doctorProfile.clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality', 'patient.user', 'period']);

        // Apply filters
        if (!empty($filters['doctor_profile_id'])) {
            $query->where('doctor_profile_id', $filters['doctor_profile_id']);
        }

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereHas('period', function ($q) use ($filters) {
                $q->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
            });
        }

        if (!empty($filters['patient_name'])) {
            $query->whereHas('patient.user', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['patient_name'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    public function data($filters = [])
    {
        $clinicId = auth('clinic')->user()->clinic_id;

        $query = Appointment::query()
            ->whereHas('doctorProfile.clinicUser', function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId);
            })
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality', 'patient.user', 'period']);

        // Apply filters
        if (!empty($filters['doctor_profile_id'])) {
            $query->where('doctor_profile_id', $filters['doctor_profile_id']);
        }

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereHas('period', function ($q) use ($filters) {
                $q->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
            });
        }

        return datatables()->of($query)
            ->addColumn('doctor_name', function ($item) {
                return $item->doctorProfile->name ?? 'N/A';
            })
            ->addColumn('patient_name', function ($item) {
                return $item->patient->user->name ?? 'N/A';
            })
            ->addColumn('appointment_date', function ($item) {
                return $item->period ? $item->period->date->format('Y-m-d') : 'N/A';
            })
            ->addColumn('appointment_time', function ($item) {
                return $item->period ? $item->period->start_time . ' - ' . $item->period->end_time : 'N/A';
            })
            ->addColumn('visit_type', function ($item) {
                return $item->visit_type_label;
            })
            ->addColumn('slot_number', function ($item) {
                return $item->slot_number ?? '-';
            })
            ->editColumn('status', function ($item) {
                $statusClasses = [
                    'pending' => 'warning',
                    'confirmed' => 'success',
                    'cancelled' => 'danger',
                    'expired' => 'secondary',
                    'waiting' => 'info',
                    'completed' => 'primary',
                ];
                $class = $statusClasses[$item->status] ?? 'secondary';
                return '<span class="badge bg-' . $class . '">' . ucfirst($item->status) . '</span>';
            })
            ->addColumn('action', fn($item) => $this->actionButtons($item))
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(array $data)
    {
        try {
            DB::beginTransaction();

            // Set default values if not provided
            if (!isset($data['status'])) {
                $data['status'] = Appointment::STATUS_CONFIRMED;
            }

            if (!isset($data['visit_type'])) {
                $data['visit_type'] = 0; // Default to Initial Visit
            }

            // Set booked_at for confirmed appointments
            if ($data['status'] === Appointment::STATUS_CONFIRMED) {
                $data['booked_at'] = now();
            }

            $appointment = Appointment::create($data);

            // Increment period booked count if confirmed
            if ($data['status'] === Appointment::STATUS_CONFIRMED && isset($data['period_id'])) {
                $appointment->period->incrementBookedCount();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('Appointment created successfully'),
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
            $appointment = $this->find($id);
            $this->assertAppointmentBelongsToClinic($appointment);

            $oldStatus = $appointment->status;
            $oldPeriodId = $appointment->period_id;

            // Only update fields that are provided
            $appointment->update(array_filter($data, function($value) {
                return $value !== null;
            }));

            // Handle period change (only if period_id is in the data)
            if (isset($data['period_id']) && $oldPeriodId != $data['period_id']) {
                // Decrement old period if was confirmed
                if ($oldStatus === Appointment::STATUS_CONFIRMED) {
                    $oldPeriod = DailyPeriod::find($oldPeriodId);
                    if ($oldPeriod) {
                        $oldPeriod->decrementBookedCount();
                    }
                }
                // Increment new period if is confirmed
                if ($appointment->status === Appointment::STATUS_CONFIRMED) {
                    $appointment->period->incrementBookedCount();
                }
            }

            // Handle status change
            if (isset($data['status']) && $oldStatus != $data['status']) {
                if ($data['status'] === Appointment::STATUS_CONFIRMED && $oldStatus !== Appointment::STATUS_CONFIRMED) {
                    $appointment->period->incrementBookedCount();
                    $appointment->update(['booked_at' => now()]);
                } elseif ($oldStatus === Appointment::STATUS_CONFIRMED && $data['status'] !== Appointment::STATUS_CONFIRMED) {
                    $appointment->period->decrementBookedCount();
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => __('Appointment updated successfully'),
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
        $appointment = $this->find($id);
        $this->assertAppointmentBelongsToClinic($appointment);

        return $appointment->delete();
    }

    public function find($id)
    {
        return Appointment::findOrFail($id);
    }

    public function forDoctor($doctorProfileId, $filters = [])
    {
        $this->assertDoctorBelongsToClinic($doctorProfileId);

        $query = Appointment::where('doctor_profile_id', $doctorProfileId)
            ->with(['patient.user', 'period']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereHas('period', function ($q) use ($filters) {
                $q->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function forPatient($patientId, $filters = [])
    {
        $query = Appointment::where('patient_id', $patientId)
            ->with(['doctorProfile.clinicUser', 'doctorProfile.speciality', 'period']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function forPeriod($periodId)
    {
        return Appointment::where('period_id', $periodId)
            ->with(['patient.user', 'doctorProfile'])
            ->orderBy('slot_number')
            ->orderBy('created_at')
            ->get();
    }

    public function confirm($id)
    {
        return DB::transaction(function () use ($id) {
            $appointment = $this->find($id);
            $this->assertAppointmentBelongsToClinic($appointment);

            $appointment->confirm();
            return $appointment->fresh();
        });
    }

    public function cancel($id, $reason = null, $cancelledBy = null)
    {
        return DB::transaction(function () use ($id, $reason, $cancelledBy) {
            $appointment = $this->find($id);
            $this->assertAppointmentBelongsToClinic($appointment);

            $appointment->cancel($reason, $cancelledBy);
            return $appointment->fresh();
        });
    }

    public function findByConfirmationCode($code)
    {
        return Appointment::where('confirmation_code', $code)->first();
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

    private function assertAppointmentBelongsToClinic($appointment): void
    {
        $this->assertDoctorBelongsToClinic($appointment->doctor_profile_id);
    }

    private function actionButtons($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="editAppointment({$item->id})" class="btn btn-sm btn-warning text-white" title="Edit"><i class="fa fa-edit"></i></button>
            <button onclick="viewAppointment({$item->id})" class="btn btn-sm btn-info text-white" title="View"><i class="fa fa-eye"></i></button>
        </div>
        HTML;
    }
}

