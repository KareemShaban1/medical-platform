<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorPatient extends Model
{
    use HasFactory;
    protected $table = "doctor_patient";
    protected $fillable = [
        'doctor_profile_id',
        'patient_id',
        'clinic_id',
    ];


    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
