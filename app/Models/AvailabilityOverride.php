<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AvailabilityOverride extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'doctor_profile_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'note',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    const TYPE_BLOCKED = 'blocked';
    const TYPE_OPENED = 'opened';

    public static function getTypes()
    {
        return [
            self::TYPE_BLOCKED => 'Blocked',
            self::TYPE_OPENED => 'Opened',
        ];
    }

    // Relationships
    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
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

    public function scopeBlocked($query)
    {
        return $query->where('type', self::TYPE_BLOCKED);
    }

    public function scopeOpened($query)
    {
        return $query->where('type', self::TYPE_OPENED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Helper methods
    public function isBlocked()
    {
        return $this->type === self::TYPE_BLOCKED;
    }

    public function isOpened()
    {
        return $this->type === self::TYPE_OPENED;
    }
}

