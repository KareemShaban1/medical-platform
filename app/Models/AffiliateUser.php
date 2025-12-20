<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class AffiliateUser extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'affiliate';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    public function affiliateCode()
    {
        return $this->morphOne(AffiliateCode::class, 'affiliateable');
    }
}
