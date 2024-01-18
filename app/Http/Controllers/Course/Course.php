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
            'course' => $course,
            'owner' => User::find($course->owner),
            'user_is_owner' => ($request->user()->id === $course->owner)
        ]);
    }
}
