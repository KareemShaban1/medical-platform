<?php

namespace App\Models;

use App\Models\Traits\HasOTP;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    use HasOTP;

    protected $fillable = [
        'otpable_id',
        'otpable_type',
        'otp',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('expires_at', '>', now())->where('is_used', false);
        });
    }

    public function otpable()
    {
        return $this->morphTo();
    }
}
