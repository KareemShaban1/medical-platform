<?php

namespace App\Models\Traits;


trait HasOTP {
 

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
