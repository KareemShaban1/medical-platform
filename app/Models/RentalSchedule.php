<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_space_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    /**
     * Days of week for dropdowns
     */
    public const DAYS = [
        'sunday' => 'Sunday',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
    ];

    /**
     * Get the rental space
     */
    public function rentalSpace()
    {
        return $this->belongsTo(RentalSpace::class);
    }

    /**
     * Scope for specific day
     */
    public function scopeForDay($query, string $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    /**
     * Scope for available schedules
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        $start = is_string($this->start_time) ? $this->start_time : ($this->start_time ? $this->start_time->format('H:i') : '');
        $end = is_string($this->end_time) ? $this->end_time : ($this->end_time ? $this->end_time->format('H:i') : '');

        if (!$start || !$end) return '';

        return date('g:i A', strtotime($start)) . ' - ' . date('g:i A', strtotime($end));
    }

    /**
     * Get day name
     */
    public function getDayNameAttribute(): string
    {
        return __(ucfirst($this->day_of_week));
    }
}
