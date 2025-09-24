<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class SupplierUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\SupplierUserFactory> */
    use HasFactory, HasRoles, SoftDeletes , Notifiable;

    protected $guard_name = 'supplier';

    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function otps()
    {
        return $this->morphMany(UserOtp::class, 'otpable');
    }

}
