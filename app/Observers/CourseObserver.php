<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Str;
class CourseObserver
{
    //
    public function creating(Course $course)
    {
        $course->slug_ar = Str::slug($course->title_ar);
        $course->slug_en = Str::slug($course->title_en);
    }
    public function updating(Course $course)
    {
        $course->slug_ar = Str::slug($course->title_ar);
        $course->slug_en = Str::slug($course->title_en);
    }
}