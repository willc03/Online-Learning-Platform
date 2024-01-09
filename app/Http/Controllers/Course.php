<?php

namespace App\Http\Controllers;

use App\Models\Course as CourseModel; // 'as' used due to duplicate definition of key word 'Course'
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Http\Request;

class Course extends Controller
{
    // Create a function for the home page route
    public function index(Request $request)
    {
        // Check for a valid course
        $url_course_id = explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2];
        if (!$url_course_id || !$course = CourseModel::find($url_course_id)) {
            return redirect()->to(route('home'))->withErrors(['INVALID_COURSE' => 'Course Error - The requested course could not be found.']);
        }
        // Check the user is a valid member of the course
        $user_id = $request->user()->id;
        if (!UserCourse::where(['course_id' => $url_course_id, 'user_id' => $user_id])->exists() && $user_id !== $course->owner) {
            return redirect()->to(route('home'))->withErrors(['NOT_COURSE_MEMBER' => 'Course Error - You aren\'t a member of this course!']);
        }
        // Present the course home page to the user
        return view('courses.home', [
            'course_name' => $course->title,
            'course_description' => $course->description ?? null,
            'course_owner' => User::find($course->owner)->name ?? null,
            'course_sections' => $course->sections,
        ]);
    }
}
