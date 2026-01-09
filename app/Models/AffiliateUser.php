<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasTickets;

class AffiliateUser extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes, HasTickets;

    protected $guard_name = 'affiliate';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function affiliateCode()
    {
        return $this->morphOne(AffiliateCode::class, 'affiliateable');
    }

    public function otps()
    {
        return $this->morphMany(UserOtp::class, 'otpable');
    }
}
