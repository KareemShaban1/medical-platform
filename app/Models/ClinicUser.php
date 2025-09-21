<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Role;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class ClinicUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\ClinicUserFactory> */
    use HasFactory, HasRoles , SoftDeletes;

    protected $guard_name = 'clinic';

    protected $fillable = [
        'clinic_id',
        'name',
        'email',
        'phone',
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
