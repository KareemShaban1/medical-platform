<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $patient = auth('patient')->user();
        $user = $patient->user;
        return view('frontend.patient.profile.index', compact('patient', 'user'));
    }

    public function update(Request $request)
    {
        $patient = auth('patient')->user();
        $user = $patient->user;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            // 'phone' => ['required', 'string', 'max:30'],
        ]);

        if ($user) {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->save();
        }

        // $patient->phone = $data['phone'];
        // $patient->save();

        return redirect()->route('user.profile.index')->with('success', __('Profile updated successfully'));
    }

    public function password()
    {
        $patient = auth('patient')->user();
        $user = $patient->user;
        return view('frontend.patient.profile.password', compact('patient', 'user'));
    }

    public function updatePassword(Request $request)
    {
        $patient = auth('patient')->user();
        $user = $patient->user;

        $data = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        if (!$user || !Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => __('Current password is incorrect')]);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return redirect()->route('user.profile.password')->with('success', __('Password updated successfully'));
    }
}
