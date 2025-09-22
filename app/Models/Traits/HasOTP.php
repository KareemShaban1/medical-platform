<?php

namespace App\Models\Traits;


trait HasOTP {
    public static function bootHasOtp()
    {
        static::creating(function ($model) {
            $model->otp        = $model->generateOtp();
            $model->expires_at = now()->addMinutes(5);
        });
    }

    public function generateOtp()
    {
        $otp = rand(100000, 999999);
        while ($this->otpExists($otp)) {
            $otp = rand(100000, 999999);
        }
        return $otp;
    }

    public function otpExists($otp)
    {
        return static::where('otp', $otp)->exists();
    }
}
