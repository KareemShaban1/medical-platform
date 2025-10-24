<?php

namespace App\Models;

use App\Enums\VisitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_profile_id',
        'patient_id',
        'period_id',
        'slot_number',
        'status',
        'confirmation_code',
        'confirmation_code_expires_at',
        'booked_at',
        'patient_notes',
        'doctor_notes',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'visit_type',
        'cost_amount',
        'payment_status',
    ];

    protected $casts = [
        'confirmation_code_expires_at' => 'datetime',
        'booked_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'visit_type' => VisitType::class,
        'cost_amount' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';
    const STATUS_WAITING = 'waiting';
    const STATUS_COMPLETED = 'completed';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_WAITING => 'Waiting',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function (Appointment $appointment) {
            if ($appointment->isDirty('status') && $appointment->status === self::STATUS_COMPLETED) {
                $clinicId = optional($appointment->doctorProfile->clinic)->id;
                if (!$clinicId) {
                    return;
                }

                MedicalRecord::firstOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'clinic_id' => $clinicId,
                        'doctor_profile_id' => $appointment->doctor_profile_id,
                        'patient_id' => $appointment->patient_id,
                        'visit_type' => ($appointment->visit_type ?? 0),
                        'notes' => $appointment->doctor_notes,
                        'created_by' => auth('clinic')->id() ?: null,
                    ]
                );
            }
        });
    }


    // Relationships
    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function period()
    {
        return $this->belongsTo(DailyPeriod::class, 'period_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // Scopes
    public function scopeForDoctor($query, $doctorProfileId)
    {
        return $query->where('doctor_profile_id', $doctorProfileId);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeForPeriod($query, $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereHas('period', function ($q) {
            $q->where('date', '>=', now()->toDateString());
        });
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereHas('period', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        });
    }

    // Helper methods
    public function generateConfirmationCode()
    {
        $this->confirmation_code = strtoupper(Str::random(6));
        $this->confirmation_code_expires_at = now()->addMinutes(30);
        $this->save();
    }

    public function confirm()
    {
        $wasConfirmed = $this->isConfirmed();

        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'booked_at' => now(),
        ]);

        // Only increment if this is a new confirmation (not already confirmed)
        if (!$wasConfirmed) {
            $this->period->incrementBookedCount();
        }
    }

    public function cancel($reason = null, $cancelledBy = null)
    {
        $wasConfirmed = $this->isConfirmed();

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
            'cancelled_at' => now(),
        ]);

        // Only decrement period booked count if appointment was confirmed
        if ($wasConfirmed) {
            $this->period->decrementBookedCount();
        }
    }

    public function expire()
    {
        $wasConfirmed = $this->isConfirmed();

        $this->update(['status' => self::STATUS_EXPIRED]);

        // Only decrement if appointment was confirmed
        if ($wasConfirmed) {
            $this->period->decrementBookedCount();
        }
    }

    public function complete()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired()
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function isConfirmationCodeValid()
    {
        return $this->confirmation_code &&
               $this->confirmation_code_expires_at &&
               $this->confirmation_code_expires_at->isFuture();
    }

    public function getStatusBadgeAttribute()
    {
        $statusClasses = [
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_CONFIRMED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            self::STATUS_EXPIRED => 'bg-secondary',
            self::STATUS_WAITING => 'bg-info',
            self::STATUS_COMPLETED => 'bg-primary',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-secondary';
        $text = self::getStatuses()[$this->status] ?? 'Unknown';

        return "<span class=\"badge {$class}\">{$text}</span>";
    }

    public function getVisitTypeLabelAttribute()
    {
        return $this->visit_type ? $this->visit_type->label() : 'N/A';
    }

    public static function getVisitTypeOptions()
    {
        return VisitType::options();
    }

    // prescription
    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
}
