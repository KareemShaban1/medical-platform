<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_recurring',
    ];

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function doctorProfile()
    {
        return $this->hasOneThrough(
            DoctorProfile::class,
            ClinicUser::class,
            'id',           // Foreign key on clinic_users table
            'clinic_user_id', // Foreign key on doctor_profiles table
            'clinic_user_id', // Local key on working_hours table
            'id'            // Local key on clinic_users table
        );
    }

    // Scopes
    public function scopeForClinicUser($query, $clinicUserId)
    {
        return $query->where('clinic_user_id', $clinicUserId);
    }

    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }
}

