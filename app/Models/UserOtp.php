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
        static::creating(function ($model) {
            $model->otp = $model->generateOtp();
            $model->expires_at = now()->addMinutes(5);
        });
    }

    public function otpable()
    {
        return $this->morphTo();
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function canResend()
    {
        // Simple check - allow resend if OTP is not used and not expired
        return !$this->is_used && !$this->isExpired();
    }

    public function verify($inputOtp)
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->is_used) {
            return false;
        }

        if ($this->otp !== $inputOtp) {
            return false;
        }

        $this->update(['is_used' => true]);
        return true;
    }

    public function resend()
    {
        // Update expiry time for resend
        $this->update([
            'expires_at' => now()->addMinutes(5)
        ]);
    }
}
