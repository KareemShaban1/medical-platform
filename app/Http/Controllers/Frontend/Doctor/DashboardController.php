<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->has_clinic) {
            return redirect()->route('home');
        }

        // Get orders made by this doctor
        $orders = Order::where('clinic_user_id', $doctor->id)
            ->latest()
            ->take(5)
            ->get();

        return view('frontend.doctor.dashboard', compact('doctor', 'orders'));
    }
}
