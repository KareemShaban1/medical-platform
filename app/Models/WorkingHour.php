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
}

