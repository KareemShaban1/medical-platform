<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Models\ClinicUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DoctorAuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('frontend.doctor.register');
    }

    public function register(Request $request)
    {
        // Validation rules (only step 2 fields - user info)
        $validationRules = [
            'user_name' => 'required|string|min:2',
            'user_email' => 'required|email|unique:clinic_users,email',
            'phone' => 'required|string|max:255|unique:clinic_users,phone',
            'password' => 'required|string|min:8|confirmed',
        ];

        $validator = Validator::make($request->all(), $validationRules, [
            'user_name.required' => 'User name is required.',
            'user_name.min' => 'User name must be at least 2 characters.',
            'user_email.required' => 'Email is required.',
            'user_email.email' => 'Please enter a valid email address.',
            'user_email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the form and try again.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create doctor user (without clinic)
            $user = ClinicUser::create([
                'name' => $request->user_name,
                'email' => $request->user_email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'clinic_id' => null,
                'has_clinic' => false,
                'status' => 1, // Active by default for doctors
            ]);

            DB::commit();

            // Auto login the doctor
            Auth::guard('clinic')->login($user);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Welcome to the platform.',
                'redirect_url' => route('home')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
