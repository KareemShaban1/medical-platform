<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AttendanceLog extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'clinic_user_id',
        'check_type',
        'source',
        'requested_by',
        'approved_by',
        'approved_at',
        'at',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'at' => 'datetime',
    ];

    public function clinicUser()
    {
        return $this->belongsTo(ClinicUser::class);
    }

    public function requester()
    {
        return $this->belongsTo(ClinicUser::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(ClinicUser::class, 'approved_by');
    }
}
