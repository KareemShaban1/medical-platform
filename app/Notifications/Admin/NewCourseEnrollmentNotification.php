<?php

namespace App\Notifications\Admin;

use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCourseEnrollmentNotification extends Notification
{
    use Queueable;

    public function __construct(public CourseEnrollment $enrollment)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $course = $this->enrollment->course;
        $user = $this->enrollment->clinicUser;

        return [
            'title' => 'New Course Enrollment',
            'message' => sprintf('"%s" enrolled in course "%s"', $user?->name ?? 'Clinic User', $course?->title ?? '#'.$this->enrollment->course_id),
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'clinic_user_id' => $this->enrollment->clinic_user_id,
            'status' => $this->enrollment->status,
            'action_url' => route('admin.course-enrollments.index', ['highlight' => $this->enrollment->id]),
            'type' => 'course_enrollment',
        ];
    }
}

