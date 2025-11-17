<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool isDoctor()
 * @method DoctorProfile|null getDoctorProfile()
 */
class ClinicUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ClinicUserFactory> */
    use HasFactory, HasRoles, SoftDeletes, Notifiable;

    protected $guard_name = 'clinic';

    protected $fillable = [
        'clinic_id',
        'has_clinic',
        'name',
        'position_title',
        'email',
        'phone',
        'password',
        'status',
        'salary_frequency',
        'amount_per_salary_frequency',
    ];


    // ------- attributes -------



    // ------- scopes -------
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeForClinic($query)
    {
        return $query->where('clinic_id', auth('clinic')->user()->clinic_id);
    }

    // ------- relations -------
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class);
    }

    public function otps()
    {
        return $this->morphMany(UserOtp::class, 'otpable');
    }

    public function salaryContract()
    {
        return $this->hasOne(SalaryContract::class);
    }

    // payslips
    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }



    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function subscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable');
    }

    // ------- helper methods -------

    /**
     * Check if this clinic user is a doctor (has a doctor profile)
     */
    public function isDoctor()
    {
        return $this->doctorProfile()->exists();
    }

    /**
     * Get the doctor profile if exists
     */
    public function getDoctorProfile()
    {
        return $this->doctorProfile;
    }

    /**
     * Check if this is a standalone doctor (clinic_id is null)
     */
    public function isStandaloneDoctor()
    {
        return $this->clinic_id === null;
    }

}
