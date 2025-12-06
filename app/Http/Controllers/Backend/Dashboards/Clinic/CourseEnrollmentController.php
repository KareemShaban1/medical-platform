<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;

class CourseEnrollmentController extends Controller
{
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view course enrollments'), 403, __('You are not authorized to view course enrollments'));

        $enrollments = CourseEnrollment::with('course')
            ->where('clinic_user_id', auth('clinic')->id())
            ->latest()
            ->paginate(12);

        return view('backend.dashboards.clinic.pages.course-enrollments.index', compact('enrollments'));
    }
}