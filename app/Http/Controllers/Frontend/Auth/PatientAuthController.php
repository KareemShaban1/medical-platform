<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientAuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                // Check if phone is unique per clinic (if clinic_id is provided)
                // For self-registration, we don't have clinic_id yet, so just check generally
                function ($attribute, $value, $fail) {
                    $existingPatient = Patient::where('phone', $value)->first();
                    if ($existingPatient) {
                        $fail('This phone number is already registered.');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

                try {
                    DB::transaction(function () use ($request) {
                        // Create the user account first
                        $user = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'password' => Hash::make($request->password),
                        ]);

                        // Create the patient record linked to the user with phone
                        Patient::create([
                            'user_id' => $user->id,
                            'phone' => $request->phone,
                        ]);
                    });

            return redirect()->route('login')->with('success', __('Registration successful! Please login to continue.'));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => __('Registration failed. Please try again.')])->withInput();
        }
    }
}
