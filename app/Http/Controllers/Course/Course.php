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
        // Get the course id (the existence is processed through the custom Course middleware)
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

    // Create a function for the settings page of the course
    public function settings(Request $request)
    {
        // Get the course from the ID
        $url_course_id = explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2];
        $course = CourseModel::find($url_course_id);
        // Return the view to the user
        return view('welcome'); // This will be replaced later when the page is defined.
    }

    // Add a function to allow AJAX requests to get forms
    public function formRequest(Request $request)
    {
        // Validation
        $validated_data = $request->validate([
            'form_type' => ['required', 'string', 'in:text,lesson,file,image'],
            'section_id' => ['required', 'string', 'exists:sections,id'],
            'course_id' => ['required', 'string', 'exists:courses,id']
        ]);
        // Get the course
        $url_course_id = explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2];
        $course = CourseModel::find($url_course_id);
        // Ensure the user has access
        if (!Gate::allows('course-edit', $course)) {
            return response("You don't have permission to edit this course!", 403);
        }
        // Get the form
        return match ($validated_data['form_type']) {
            'text' => view('components.courses.component_add.text', ['courseId' => $course->id, 'sectionId' => $validated_data['section_id']]),
            'lesson' => view('components.courses.component_add.lesson', ['courseId' => $course->id, 'sectionId' => $validated_data['section_id']]),
            'file' => view('components.courses.component_add.file', ['courseId' => $course->id, 'sectionId' => $validated_data['section_id']]),
            'image' => view('components.courses.component_add.image', ['courseId' => $course->id, 'sectionId' => $validated_data['section_id']]),
            default => 400,
        };

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
            return response("You don't have permission to edit this course!", 403);
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
                    return response("Validation failed!", 403);
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
                    return response('Validation failed', 403);
                }

                $newSection = new Section;
                $newSection->title = $validated_data['data'][0]['value'];

                if (isset($validated_data['data'][1]['name'])) {
                    $newSection->description = $validated_data['data'][1]['value'] ?? null;
                }

                $newSection->course_id = $course->id;
                $newSection->position = $course->sections->max('position') + 1;

                $newSection->save();

                return response(['SUCCESS', $newSection->id], 200);
                break;
            case "delete_section":
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data.section_id' => ['required', 'string', 'exists:sections,id']
                ]);

                // Ensure validation is successful
                if ($interior_validation->fails()) {
                    return response("Validation failed!", 403);
                }

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

                return response(200, 200);
                break;
            case "section_item_add":
                // Re-organise the data field
                $validated_data['data'] = array_column($validated_data['data'], 'value', 'name');
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data' => ['required', 'array'],
                    'data.component-type' => ['required', 'string', 'in:text,lesson,image,file'],
                    'data.section-id' => ['required', 'string', 'exists:sections,id']
                ]);
                if ($interior_validation->fails()) {
                    return response('Validation failed', 403);
                }
                // Interior case for component addition types
                switch($validated_data['data']['component-type']) {
                    case "text":
                        // Validation for text
                        $textValidation = Validator::make($validated_data, [
                            'data.content' => ['required', 'string']
                        ]);
                        if ($textValidation->fails()) {
                            return response('Validation failed', 403);
                        }
                        // Create the component if successful
                        $component = new SectionItem;
                        $component->title = $validated_data['data']['content'];
                        $component->item_type = 'TEXT';
                        $component->item_value = '{}';
                        $component->position = SectionItem::where('section_id', $validated_data['data']['section-id'])->max('position') + 1;
                        $component->section_id = $validated_data['data']['section-id'];

                        $component->save();
                        return response('Text creation successful', 200);
                        break;
                    case "lesson":
                        // Validation for lesson
                        $lessonValidation = Validator::make($validated_data, [
                            'data.title' => ['required', 'string'],
                            'data.description' => ['nullable', 'string']
                        ]);
                        if ($lessonValidation->fails()) {
                            return response('Validation failed', 403);
                        }
                        // Create the component if successful
                            // Create the component
                        $component = new SectionItem;
                        $component->title = $validated_data['data']['title'];
                        if ($validated_data['data']['description'] != null) {
                            $component->description = $validated_data['data']['description'];
                        }
                        $component->item_type = 'LESSON';
                        $component->item_value = '{}';
                        $component->position = SectionItem::where('section_id', $validated_data['data']['section-id'])->max('position') + 1;
                        $component->section_id = $validated_data['data']['section-id'];
                        $componentSaved = $component->save();
                        if ($componentSaved) {
                            // Create a lesson
                            $lesson = new Lesson;
                            $lesson->title = $validated_data['data']['title'];
                            if ($validated_data['data']['description'] != null) {
                                $lesson->description = $validated_data['data']['description'];
                            }
                            $lesson->section_item_id = $component->id;
                            if ($lesson->save()) {
                                $component->item_value = Json::encode(['lesson_id' => $lesson->id]);
                                return response('Lesson creation successful', 200);
                            } else {
                                return response('Encountered an error!', 403);
                            }
                        }
                        break;
                }
            case "section_item_delete":
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data' => ['required', 'array'],
                    'data.item_id' => ['required', 'string', 'exists:section_items,id']
                ]);
                if ($interior_validation->fails()) {
                    return response($interior_validation->errors(), 403);
                }
                // Delete the item if validation is successful
                $didDelete = SectionItem::where('id', $validated_data['data']['item_id'])->delete();
                if ($didDelete) {
                    return response("Section removal successful", 200);
                } else {
                    return response("Couldn't delete the section", 500);
                }
                break;
            case "section_item_move":
                // Further validation
                $interior_validation = Validator::make($validated_data, [
                    'data' => ['required', 'array'],
                    'data.item_id' => ['required', 'string', 'exists:section_items,id'],
                    'data.direction' => ['required', 'string']
                ]);
                if ($interior_validation->fails()) {
                    return response($interior_validation->errors(), 400);
                }
                // Move the item if validation is successful
                $item = SectionItem::where('id', $validated_data['data']['item_id'])->firstOrFail();
                $section = Section::where('id', $item->section_id)->firstOrFail();
                if ($validated_data['data']['direction'] === 'down') {
                    $newSectionId = Section::where('position', '>', $section->position)->min('position');
                } else {
                    $newSectionId = Section::where('position', '<', $section->position)->max('position');
                }
                $newSection = Section::where('position', $newSectionId)->firstOrFail();
                $item->section_id = $newSection->id;
                $item->position = $newSection->items->max('position') + 1;

                if ($item->save()) {
                    return response("Move successful", 200);
                }
                return response("TESTING", 400);
        }
        // Returns
        return 200;
    }
}
