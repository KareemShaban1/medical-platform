<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
    ];



    /**
     * Get the user associated with the patient (for registered users)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get OTPs for this patient
     */
    public function otps()
    {
        return $this->morphMany(UserOtp::class, 'otpable');
    }

    // ------- Scopes -------

    /**
     * Scope to get patients for a specific clinic
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Scope to get patients with linked user accounts (all patients now)
     */
    public function scopeRegistered($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope to get patients without linked user accounts (no longer applicable)
     */
    public function scopeClinicCreated($query)
    {
        return $query->whereNull('user_id'); // This will return empty results
    }

    // ------- Accessor Methods -------

    /**
     * Get the patient's name from the linked user
     */
    public function getNameAttribute()
    {
        // Make sure to load the user relationship if not already loaded
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        return $this->user ? $this->user->name : null;
    }

    /**
     * Get the patient's email from the linked user
     */
    public function getEmailAttribute()
    {
        // Make sure to load the user relationship if not already loaded
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        return $this->user ? $this->user->email : null;
    }


    // ------- Helper Methods -------

    /**
     * Check if the patient is linked to a user account
     */
    public function isRegistered()
    {
        return !is_null($this->user_id) && !is_null($this->user);
    }

    /**
     * Check if patient was created by clinic (this will no longer be possible)
     */
    public function isClinicCreated()
    {
        return false; // All patients are now linked to users
    }

    /**
     * Get the display name for the patient
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get the status badge for the patient
     */
    public function getStatusBadgeAttribute()
    {
        return '<span class="badge bg-success">Registered User</span>';
    }
}
