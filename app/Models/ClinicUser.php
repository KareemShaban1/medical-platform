<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ClinicUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ClinicUserFactory> */
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'password',
        'status',
        'salary_frequency',
        'amount_per_salary_frequency',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}