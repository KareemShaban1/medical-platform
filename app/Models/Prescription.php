<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Prescription extends Model implements HasMedia
{
    use  HasFactory, InteractsWithMedia;

    protected $fillable = [
        'appointment_id',
        'clinic_id',
        'patient_id',
        'doctor_profile_id',
        'notes',
    ];

    public $appends = ['images'];
    
    public function getImagesAttribute()
    {
        return $this->getMedia('prescription_images')->map(function ($media) {
            return $media->getUrl();
        })->toArray();
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}