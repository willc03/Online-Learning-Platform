<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course as CourseModel;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Section;
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
            'user_is_owner' => ($request->user()->id === $course->owner),
            'is_editing' => (($request->user()->id === $course->owner) && $request->has('editing') && $request->input('editing') === 'true')
        ]);
    }

    // Add a function to allow AJAX requests to edit
    public function contentEdit(Request $request) {
        // Run validation
        $validated_data = $request->validate([
            'course_id' => ['string', 'required', 'exists:courses,id'],
            'edit_type' => ['string', 'required'],
            'data' => ['required', 'json'],
            'section_id' => ['nullable']
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
            case "new_section":
                if ( isset($validated_data['data'][0]['name']) && $validated_data['data'][0]['name'] == 'title' ) {
                    $newSection = new Section;
                    $newSection->title = $validated_data['data'][0]['value'];

                    if (isset($validated_data['data'][1]['name']) && $validated_data['data'][1]['name'] == 'description') {
                        $newSection->description = $validated_data['data'][1]['value'] ?? null;
                    }

                    $newSection->course_id = $course->id;
                    $newSection->position = $course->sections->count();

                    $newSection->save();

                    return ['SUCCESS', $newSection->id];
                }
                break;
            case "delete_section":
                if ( isset($validated_data['data']['section_id']) && Section::where('id', $validated_data['data']['section_id'])->exists() ) {
                    $section = Section::find($validated_data['data']['section_id']);
                    foreach ($section->items as $item) { // Lessons must be manually deleted as a direct relationship couldn't be established
                        if ($item->item_type == "LESSON" && $lesson = Lesson::where('section_item_id', $section->id)) {
                            $lesson->delete();
                        }
                    }
                    $section->delete();
                    return "SUCCESS";
                }
                break;
        }
        // Returns
        return 200;
    }
}
