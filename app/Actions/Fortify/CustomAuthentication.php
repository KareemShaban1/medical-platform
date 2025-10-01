<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\ClinicUser;
use App\Models\SupplierUser;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomAuthentication
{


    public function authenticateClinicUser($request)
    {
        $email = $request->email;
        $password = $request->password;

        $user = ClinicUser::where('email', '=', $email)
        ->whereHas('clinic',function($query){
            $query->where('status', 1)
            ->where('is_allowed', 1);
        })
        ->first();

        $unverifiedUser = ClinicUser::where('email', '=', $email)
        ->whereHas('clinic',function($query){
            $query->where('is_allowed', 0);
        })
        ->first();

        if ($unverifiedUser) {
            throw ValidationException::withMessages([
                'email' => 'You are not verified. Please complete your registration to login.',
            ]);
        }


        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function authenticateSupplierUser($request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = SupplierUser::where('email', '=', $email)
        ->whereHas('supplier',function($query){
            $query->where(['status' => 1, 'is_allowed' => 1]);

        })
        ->first();

        $unverifiedUser = SupplierUser::where('email', '=', $email)
        ->whereHas('supplier',function($query){
            $query->where('is_allowed', 0);
        })
        ->first();


        if ($unverifiedUser) {
            throw ValidationException::withMessages([
                'email' => 'You are not verified. Please complete your registration to login.',
            ]);
        }

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }
        return false;
    }


    public function authenticatePatient($request)
    {
        $email = $request->email;
        $password = $request->password;

        // Find patient by email through their linked user account
        $patient = Patient::with('user')
            ->whereHas('user', function($query) use ($email) {
                $query->where('email', $email);
            })
            ->first();

        if ($patient && $patient->user && Hash::check($password, $patient->user->password)) {
            return $patient;
        }

        return false;
    }

    public function authenticateAdmin($request)
    {


        $request->validate(
            [
                'email' => ['required','email'],
                'password' => ['required'],
            ],
            [
                'email.required' => 'برجاء أدخال البريد الألكترونى',
                'password.required' => 'برجاء أدخال كلمة المرور',

            ]
        );


        $email = $request->email;
        $password = $request->password;
        $admin = Admin::where('email', '=', $email)->first();

        if ($admin && Hash::check($password, $admin->password)) {
            return $admin;
        }
        return false;
    }
}
