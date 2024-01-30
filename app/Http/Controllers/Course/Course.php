<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course as CourseModel;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

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
            'section_id' => ['nullable', 'exists:sections,id']
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
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data' => ['required', 'array'],
                    'data.*' => ['required', 'array'],
                    'data.*.0' => ['required', 'integer'],
                    'data.*.1' => ['required', 'string', 'exists:sections,id']
                ]);

                // Ensure validation is successful
                if ($interior_validation->fails()) {
                    return 403;
                }

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
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data' => ['required', 'array'],
                    'data.0' => ['required', 'array'],
                    'data.0.name' => ['required', 'string', 'in:title'],
                    'data.0.value' => ['required', 'string'],
                    'data.1' => ['nullable', 'array'],
                    'data.1.name' => ['nullable', 'string', 'in:description'],
                    'data.1.value' => ['nullable', 'string']
                ]);

                // Ensure validation is successful
                if ($interior_validation->fails()) {
                    return 403;
                }

                $newSection = new Section;
                $newSection->title = $validated_data['data'][0]['value'];

                if (isset($validated_data['data'][1]['name'])) {
                    $newSection->description = $validated_data['data'][1]['value'] ?? null;
                }

                $newSection->course_id = $course->id;
                $newSection->position = $course->sections->count();

                $newSection->save();

                return ['SUCCESS', $newSection->id];
                break;
            case "delete_section":
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data.section_id' => ['required', 'string', 'exists:sections,id']
                ]);

                $section = Section::find($validated_data['data']['section_id']);
                foreach ($section->items as $item) { // Lessons must be manually deleted as a direct relationship couldn't be established
                    if ($item->item_type == "LESSON" && $lesson = Lesson::where('section_item_id', $section->id)) {
                        $lesson->delete();
                    }
                }
                $section->delete();
                
                return "SUCCESS";
                break;
            case "section_interior_order":
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data.*' => ['required', 'array'],
                    'data.*.0' => ['required', 'numeric'],
                    'data.*.1' => ['required', 'string', 'exists:section_items,id'],
                    'section_id' => ['required', 'string', 'exists:sections,id']
                ]);

                // Ensure validation is successful
                if ( $interior_validation->fails() ) {
                    return $interior_validation->errors();
                }

                // Commence updates
                foreach ($validated_data['data'] as $item) {
                    $section_item = SectionItem::where(['section_id' => $validated_data['section_id'], 'id' => $item[1]])->firstOrFail();
                    echo $section_item->position . '\n';
                    $section_item->position = $item[0];
                    $section_item->save();
                    echo $section_item->position . '\n';
                }

                return 200;
                break;
        }
        // Returns
        return 200;
    }
}
