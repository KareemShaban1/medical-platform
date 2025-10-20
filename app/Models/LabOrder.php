<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LabOrder extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'clinic_user_id',
        'doctor_profile_id',
        'test_name',
        'lab_name',
        'status',
        'cost_amount',
        'notes',
        'result_comment',
        'sent_at',
        'received_at',
        'reviewed_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'cost_amount' => 'decimal:2',
    ];

    protected $appends = ['attachments'];

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('lab_results')->map(function ($media) {
            return [
                'id' => $media->id,
                'name' => $media->name,
                'url' => $media->getUrl(),
                'mime_type' => $media->mime_type,
                'size' => $media->size,
            ];
        })->toArray();
    }

    // Relationships
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(ClinicUser::class, 'clinic_user_id');
    }

    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    // Scopes
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }
}
