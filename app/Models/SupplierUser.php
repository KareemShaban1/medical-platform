<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class SupplierUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\SupplierUserFactory> */
    use HasFactory, HasRoles;

    protected $guard_name = 'supplier';

    protected $fillable = [
        'supplier_id',
        'name',
        'email',
        'password',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }
}
