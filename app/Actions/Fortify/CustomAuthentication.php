<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\ClinicUser;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\Hash;

class CustomAuthentication
{


    public function authenticateClinicUser($request)
    {
        $email = $request->email;
        $password = $request->password;
        $user = ClinicUser::where('email', '=', $email)
        ->whereHas('clinic',function($query){
            $query->approved();
        })
        ->first();


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
            $query->approved();
        })
        ->first();


        if ($user && Hash::check($password, $user->password)) {
            return $user;
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
