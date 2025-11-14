<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;

class CourseEnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = CourseEnrollment::with('course')
            ->where('clinic_user_id', auth('clinic')->id())
            ->latest()
            ->paginate(12);

        return view('backend.dashboards.clinic.pages.course-enrollments.index', compact('enrollments'));
    }
}

