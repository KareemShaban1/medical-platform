<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_profile_id',
        'date',
        'start_time',
        'end_time',
        'is_open',
        'capacity',
        'booked_count',
        'auto_queue',
    ];

    protected $casts = [
        'date' => 'date',
        'is_open' => 'boolean',
        'auto_queue' => 'boolean',
        'capacity' => 'integer',
        'booked_count' => 'integer',
    ];

    // Relationships
    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'period_id');
    }

    // Scopes
    public function scopeForDoctor($query, $doctorProfileId)
    {
        return $query->where('doctor_profile_id', $doctorProfileId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_open', false);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_open', true)
            ->whereRaw('booked_count < capacity');
    }

    public function scopeFull($query)
    {
        return $query->where('is_open', true)
            ->whereRaw('booked_count >= capacity');
    }

    // Accessors
    public function getRemainingCapacityAttribute()
    {
        return max(0, $this->capacity - $this->booked_count);
    }

    public function getIsFullAttribute()
    {
        return $this->booked_count >= $this->capacity;
    }

    public function getIsAvailableAttribute()
    {
        return $this->is_open && !$this->is_full;
    }

    public function getCapacityPercentageAttribute()
    {
        if ($this->capacity == 0) return 0;
        return round(($this->booked_count / $this->capacity) * 100, 2);
    }

    // Helper methods
    public function canBook()
    {
        // Always allow booking if the period is open
        // Capacity is only for display purposes (confirmed appointments)
        return $this->is_open;
    }

    public function isPast()
    {
        $periodDateTime = \Carbon\Carbon::parse($this->date . ' ' . $this->start_time);
        return $periodDateTime->isPast();
    }

    public function incrementBookedCount()
    {
        $this->increment('booked_count');
    }

    public function decrementBookedCount()
    {
        if ($this->booked_count > 0) {
            $this->decrement('booked_count');
        }
    }
}

