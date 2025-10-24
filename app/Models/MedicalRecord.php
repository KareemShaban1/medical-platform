<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'appointment_id',
        'doctor_profile_id',
        'patient_id',
        'visit_type',
        'chief_complaint',
        'diagnosis',
        'treatment',
        'notes',
        'is_shared_with_patient',
        'created_by',
        'updated_by',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_profile_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
