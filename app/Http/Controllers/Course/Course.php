<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course as CourseModel;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Http\Request;

// 'as' used due to duplicate definition of key word 'Course'

class Course extends Controller
{
    // Create a function for the home page route
    public function index(Request $request)
    {
        // Check for a valid course
        $url_course_id = explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2];
        $course = CourseModel::find($url_course_id);
        // Present the course home page to the user
        return view('courses.home', [
            'course_name' => $course->title,
            'course_description' => $course->description ?? null,
            'course_owner' => User::find($course->owner)->name ?? null,
            'course_sections' => $course->sections,
        ]);
    }
}
