<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course as CourseModel;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    // Add a function to allow AJAX requests to edit
    public function contentEdit(Request $request) {
        // Run validation
        $validated_data = $request->validate([
            'course_id' => ['string', 'required', 'exists:courses,id'],
            'edit_type' => ['string', 'required'],
            'data' => ['required', 'json']
        ]);
        $validated_data['data'] = Json::decode($validated_data['data']);
        // Get course
        $course = CourseModel::find($validated_data['course_id']);
        // Ensure gate permissions met
        if (!Gate::allows('course-edit', $course)) {
            return [false, '403'];
        }
        // Make edits based on type
        switch ( $validated_data['edit_type'] ) {
            case "section_order":
                $course_sections = $course->sections;
                foreach ($course_sections as $section) {
                    foreach ($validated_data['data'] as $data) {
                        if ($data[1] == $section->id) {
                            $section->position = $data[0];
                            $section->save();
                        }
                    }
                }
                break;
        }
        // Returns
        return 200;
    }
}
