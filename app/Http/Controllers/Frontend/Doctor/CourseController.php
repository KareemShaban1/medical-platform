<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $doctor = Auth::guard('clinic')->user();

        if (!$doctor || $doctor->has_clinic) {
            return redirect()->route('home');
        }

        $baseQuery = CourseEnrollment::with('course')
            ->where('clinic_user_id', $doctor->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        $enrollments = (clone $baseQuery)
            ->latest()
            ->paginate(10);

        return view('frontend.doctor.courses', compact('doctor', 'enrollments', 'stats'));
    }
}


