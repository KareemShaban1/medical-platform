<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    /** @use HasFactory<\Database\Factories\CourseEnrollmentFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'clinic_user_id',
        'status', // pending, approved, rejected
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }
}

