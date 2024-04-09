<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course as CourseModel;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\User;
use App\Models\UserCompletedLesson;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

// 'as' used due to duplicate definition of key word 'Course'

class Course extends Controller
{

    /**
     * The 'all' public route will be used to display the top-level courses page which can
     * be accessed by either user or guests (although only users will be able to access the
     * courses themselves.)
     *
     * @return View The view to be displayed to the user
     */
    public function all ()
    {
        return view('public.courses', [ 'courses' => CourseModel::all() ]);
    }

    /**
     * The index public route is used to display to the user the 'home' page for a course, where
     * all the blended VLE/assessment content is displayed. Data is passed in to allow information
     * such as user high scores to be displayed
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id which is passed in by Laravel automatically
     *
     * @return View The final view delivered to the user.
     */
    public function index ( Request $request, string $id )
    {
        // Get the course id (the existence is processed through the custom Course middleware)
        $course = CourseModel::find($id);
        // Cancel any lesson if the user returns home
        session()->pull('lesson');
        // Get all high scores
        $lessonHighScores = [];
        $sections = $course->firstOrFail()->sections;
        foreach ( $sections as $section ) { // As lessons were not able to be implemented via a direct database relation, lesson high scores are retrieved via loop.
            foreach ( $section->items as $item ) {
                if ( $item->item_type == "LESSON" ) {
                    $lesson = Lesson::whereId($item->item_value['lesson_id'])->firstOrFail();
                    $completedLessonRecordsQuery = UserCompletedLesson::where([ 'lesson_id' => $lesson->id, 'user_id' => $request->user()->id ]);
                    if ( $completedLessonRecordsQuery->exists() ) { // Check the user has a high score before adding to the database
                        $lessonHighScores[] = [
                            'lessonId' => $lesson->id,
                            'score'    => $completedLessonRecordsQuery->get()->max('score'), // Loops can be saved by using SQL to get the highest score.
                        ];
                    }
                }
            }
        }
        // Present the course home page to the user
        return view('course.home', [
            'course'        => $course,
            'owner'         => User::find($course->owner),
            'user_is_owner' => ( $request->user()->id === $course->owner ),
            'is_editing'    => ( ( $request->user()->id === $course->owner ) && $request->has('editing') && $request->input('editing') === 'true' ),
            'lesson_scores' => $lessonHighScores,
        ]);
    }

    /**
     * This public route will allow the user to create their own course. Minimal details
     * are required here as the user will add content to their course separately after
     * creation.
     *
     * @param Request $request The HTTP request provided by Laravel
     *
     * @return RedirectResponse The view the user is redirected to after creation or if validation is unsuccessful
     */
    public function create ( Request $request )
    {
        // Validation of the request
        $validatedData = $request->validate([
            'title'       => [ 'required', 'string' ],
            'description' => [ 'nullable', 'string' ],
            'publicity'   => [ 'nullable', 'in:on' ],
        ]);
        // Creation of the course
        $course = new CourseModel;
        $course->title = $validatedData['title'];
        if ( array_key_exists('description', $validatedData) ) { // Only add the description if it exists (Eloquent will substitute 'null' if this isn't the case
            $course->description = $validatedData['description'];
        }
        $course->is_public = array_key_exists('publicity', $validatedData);
        $course->owner = auth()->user()->id;

        if ( $course->save() ) { // Produce a different result based on whether course creation was successful.
            return redirect()->to(route('course.home', [ 'id' => $course->id ]));
        } else {
            return back()->withErrors([ 'CREATE_FAIL' => 'An unexpected error has occurred and your course could not be saved.' ]);
        }
    }

    /**
     * The modify route will process POST requests and allow users to
     * make edits to their courses.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id
     *
     * @return RedirectResponse The redirection provided to the user after or during processing.
     */
    public function modify ( Request $request, string $id )
    {
        // Course permission check
        $course = CourseModel::find($id);
        if ( !Gate::allows('course-edit', $course) ) { // Protectively ensure the user can make edits if a new route is defined.
            return redirect()->to(route('course.home', [ 'id' => $course->id ]))->withErrors([ 'NO_EDIT_PERMISSION' => "You cannot complete this action as you do not own this course." ]);
        }
        // Request validation
        $validatedData = $request->validate([
            'title'       => [ 'required', 'string' ],
            'description' => [ 'nullable', 'string' ],
            'publicity'   => [ 'nullable', 'boolean' ],
        ]);
        // Make the requested edits
        $course->title = $validatedData['title'];
        $course->description = $validatedData['description'] ?? null;
        $course->is_public = $validatedData['publicity'] ?? 0;
        // Save the new course details and produce a redirection based on the
        if ( $course->save() ) {
            return redirect()->to(route('course.settings.get', [ 'id' => $id ]));
        } else {
            return back(500)->withErrors([ 'SAVE_ERROR' => "Your edits could not be applied. Please try again." ]);
        }
    }

    /**
     * This page will present the user with the course settings page. No defensive check
     * for course ownership is needed here as the route is subject to the 'course.owner'
     * middleware.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return RedirectResponse|View The view presented to the user
     */
    public function settings ( Request $request, string $id )
    {
        // Get the course from the ID
        $course = CourseModel::findOrFail($id);
        if ( !Gate::allows('course-edit', $course) ) { // Protectively ensure the user can make edits if a new route is defined.
            return redirect()->to(route('course.home', [ 'id' => $course->id ]))->withErrors([ 'NO_EDIT_PERMISSION' => "You cannot complete this action as you do not own this course." ]);
        }
        // Return the view to the user
        return view('course.settings', [ 'course' => $course ]); // This will be replaced later when the page is defined.
    }

    /**
     * This route will be used to allow the user to request specific forms. In this case,
     * AJAX is used to load the delivered views into the viewport.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return View|Response The view or invalid response delivered to the user
     */
    public function formRequest ( Request $request, string $id )
    {
        // Validation
        $validatedData = $request->validate([
            'form_type'  => [ 'required', 'string', 'in:text,lesson,file,image' ],
            'section_id' => [ 'required', 'string', 'exists:sections,id' ],
            'course_id'  => [ 'required', 'string', 'exists:courses,id' ],
        ]);
        // Get the course
        $course = CourseModel::find($id); // This route is not proactively defended as the user cannot actually do much with the forms themselves (and the middleware already protects it)
        // Get the form
        return match ( $validatedData['form_type'] ) {
            'text'   => view('components.form.course.text', [ 'courseId' => $course->id, 'sectionId' => $validatedData['section_id'] ]),
            'lesson' => view('components.form.course.lesson', [ 'courseId' => $course->id, 'sectionId' => $validatedData['section_id'] ]),
            'file'   => view('components.form.course.file', [ 'courseId' => $course->id, 'sectionId' => $validatedData['section_id'], 'course' => $course ]),
            'image'  => view('components.form.course.image', [ 'courseId' => $course->id, 'sectionId' => $validatedData['section_id'], 'course' => $course ]),
            default  => 400,
        };

    }

    /**
     * This route will accept POST requests to allow the user to edit certain aspects of the course. In
     * this case, the editable content is: section order, adding new sections, deleting sections, arranging
     * the content within sections, adding section components, removing components within a section, moving
     * a component to the previous/next section, and finally, editing the core details of a section.
     *
     * @param Request $request The HTTP request provided by Laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return Response|RedirectResponse The AJAX response or redirect (depending on the edit type)
     */
    public function contentEdit ( Request $request, string $id )
    {
        // Run validation
        $validatedData = $request->validate([
            'course_id'  => [ 'string', 'required', 'exists:courses,id', 'in:' . $id ],
            'edit_type'  => [ 'string', 'required' ],
            'data'       => [ 'nullable', 'json' ],
            'section_id' => [ 'nullable', 'exists:sections,id' ],
        ]);
        if ( key_exists('data', $validatedData) ) {
            $validatedData['data'] = Json::decode($validatedData['data']);
        }
        // Get course
        $course = CourseModel::find($validatedData['course_id']);
        if ( !Gate::allows('course-edit', $course) ) { // Protectively ensure the user can make edits if a new route is defined.
            return redirect()->to(route('course.home', [ 'id' => $course->id ]))->withErrors([ 'NO_EDIT_PERMISSION' => "You cannot complete this action as you do not own this course." ]);
        }
        // Make edits based on type
        switch ( $validatedData['edit_type'] ) {
            case "section_order": // Re-order all the components within the course.
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'     => [ 'required', 'array' ],
                    'data.*'   => [ 'required', 'array' ],
                    'data.*.0' => [ 'required', 'integer' ],
                    'data.*.1' => [ 'required', 'string', 'exists:sections,id' ],
                ]);

                // Ensure validation is successful
                if ( $interiorValidation->fails() ) {
                    return response("Validation failed!", 403);
                }

                // Use a DATABASE TRANSACTION due to mass editing and the possibility of failure.
                $courseSections = $course->sections;
                DB::beginTransaction(); // Begin the transaction
                try {
                    foreach ( $courseSections as $section ) { // Update the position of each section
                        foreach ( $validatedData['data'] as $data ) {
                            if ( $data[1] == $section->id ) {
                                $section->update([ 'position' => $data[0] ]);
                            }
                        }
                    }
                    DB::commit(); // Commit the changes if successful
                    return response("Successfully updated the positions", 200);
                } catch ( \Exception $exception ) {
                    DB::rollBack(); // If there is an error of any kind, roll back the database changes to protect integrity.
                    return response("Could not update the positions! " . $exception->getMessage(), 500);
                }
                break;
            case "new_section": // Adding new section to the course
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'         => [ 'required', 'array' ],
                    'data.0'       => [ 'required', 'array' ],
                    'data.0.name'  => [ 'required', 'string', 'in:title' ],
                    'data.0.value' => [ 'required', 'string' ],
                    'data.1'       => [ 'nullable', 'array' ],
                    'data.1.name'  => [ 'nullable', 'string', 'in:description' ],
                    'data.1.value' => [ 'nullable', 'string' ],
                ]);

                // Ensure validation is successful
                if ( $interiorValidation->fails() ) {
                    return response('Validation failed', 403);
                }

                // Create the new course and set the details
                $newSection = new Section;
                $newSection->title = $validatedData['data'][0]['value'];
                if ( isset($validatedData['data'][1]['name']) ) {
                    $newSection->description = $validatedData['data'][1]['value'] ?? null;
                }
                $newSection->course_id = $course->id;
                $newSection->position = $course->sections->max('position') + 1;
                // Attempt to save the new course section
                if ( $newSection->save() ) {
                    return response([ 'SUCCESS', $newSection->id ], 200);
                } else {
                    return response("Unsuccessfully created the new section. Please try again", 500);
                }
                break;
            case "delete_section":
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'            => [ 'required', 'array' ],
                    'data.section_id' => [ 'required', 'string', 'exists:sections,id' ],
                ]);

                // Ensure validation is successful
                if ( $interiorValidation->fails() ) {
                    return response("Validation failed!", 403);
                }

                // Database TRANSACTION is used due to possible failure
                DB::beginTransaction(); // Begin the transaction
                try {
                    $section = Section::find($validatedData['data']['section_id']);
                    foreach ( $section->items as $item ) { // Lessons must be manually deleted as a direct relationship couldn't be established
                        if ( $item->item_type == "LESSON" && $lesson = Lesson::where('section_item_id', $section->id) ) {
                            $lesson->delete();
                        }
                    }

                    $section->delete(); // Delete the section
                    DB::commit();       // Commit the changes if there are no errors
                    return response("Section successfully deleted", 200);
                } catch ( \Exception $exception ) {
                    DB::rollBack(); // Roll back any changes upon the detection of ANY error
                    return response("Could not delete the section! " . $exception->getMessage(), 500);
                }
                break;
            case "section_interior_order":
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'       => [ 'required', 'array' ],
                    'data.*'     => [ 'required', 'array' ],
                    'data.*.0'   => [ 'required', 'numeric' ],
                    'data.*.1'   => [ 'required', 'string', 'exists:section_items,id' ],
                    'section_id' => [ 'required', 'string', 'exists:sections,id' ],
                ]);

                // Ensure validation is successful
                if ( $interiorValidation->fails() ) {
                    return $interiorValidation->errors();
                }

                // Commence updates using a database TRANSACTION
                DB::beginTransaction();
                try {
                    foreach ( $validatedData['data'] as $item ) {
                        $section_item = SectionItem::where([ 'section_id' => $validatedData['section_id'], 'id' => $item[1] ])->firstOrFail();
                        $section_item->update([ 'position' => $item[0] ]);
                    }
                    DB::commit(); // Commit the changes if they were successful
                    return response("Successfully updated interior order", 200);
                } catch ( \Exception $exception ) {
                    DB::rollBack(); // Rollback the changes if there is an error
                    return response("Could not update the interior order! " . $exception->getMessage(), 500);
                }
                break;
            case "section_item_add":
                // Re-organise the data field
                $validatedData['data'] = array_column($validatedData['data'], 'value', 'name');
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'                => [ 'required', 'array' ],
                    'data.component-type' => [ 'required', 'string', 'in:text,lesson,image,file' ],
                    'data.section-id'     => [ 'required', 'string', 'exists:sections,id' ],
                ]);
                if ( $interiorValidation->fails() ) {
                    return response('Validation failed', 403);
                }
                // Interior case for component addition types
                switch ( $validatedData['data']['component-type'] ) {
                    case "text":
                        // Validation for text
                        $textValidation = Validator::make($validatedData, [
                            'data.content' => [ 'required', 'string' ],
                        ]);
                        if ( $textValidation->fails() ) {
                            return response('Validation failed', 403);
                        }
                        // Create the component if successful
                        $component = new SectionItem;
                        $component->title = 'TEXT_ELEMENT';
                        $component->item_type = 'TEXT';
                        $component->item_value = ['text' => $validatedData['data']['content']];
                        $component->position = SectionItem::where('section_id', $validatedData['data']['section-id'])->max('position') + 1;
                        $component->section_id = $validatedData['data']['section-id'];
                        // Attempt to save the component
                        if ( $component->save() ) {
                            return response('Text creation successful', 200);
                        } else {
                            return response("Text creation NOT successful", 500);
                        }
                        break;
                    case "lesson":
                        // Validation for lesson
                        $lessonValidation = Validator::make($validatedData, [
                            'data.title'       => [ 'required', 'string' ],
                            'data.description' => [ 'nullable', 'string' ],
                        ]);
                        if ( $lessonValidation->fails() ) {
                            return response('Validation failed', 403);
                        }
                        // Create the component if successful
                        // Create the component
                        $component = new SectionItem;
                        $component->title = $validatedData['data']['title'];
                        if ( $validatedData['data']['description'] != null ) {
                            $component->description = $validatedData['data']['description'];
                        }
                        $component->item_type = 'LESSON';
                        $component->item_value = [];
                        $component->position = SectionItem::where('section_id', $validatedData['data']['section-id'])->max('position') + 1;
                        $component->section_id = $validatedData['data']['section-id'];
                        if ( $component->save() ) {
                            // Create a lesson
                            $lesson = new Lesson;
                            $lesson->title = $validatedData['data']['title'];
                            if ( $validatedData['data']['description'] != null ) {
                                $lesson->description = $validatedData['data']['description'];
                            }
                            $lesson->section_item_id = $component->id;
                            if ( $lesson->save() ) {
                                $component->item_value = [
                                    'lesson_id' => $lesson->id,
                                ];
                                $component->save();
                                return response('Lesson creation successful', 200);
                            } else {
                                return response('Encountered an error!', 403);
                            }
                        }
                        return response("Unknown server error.", 500);
                        break;
                    case "file":
                        // Validation for file
                        $fileValidation = Validator::make($validatedData, [
                            'data'       => [ 'required', 'array' ],
                            'data.file'  => [ 'required', 'string', 'exists:course_files,id' ],
                            'data.title' => [ 'required', 'string' ],
                        ]);
                        if ( $fileValidation->fails() ) {
                            return response($fileValidation->errors(), 400);
                        }
                        // Create the new component
                        $component = new SectionItem;
                        $component->title = $validatedData['data']['title'];
                        $component->item_type = "FILE";
                        $component->item_value = [ 'fileId' => $validatedData['data']['file'] ];
                        $component->position = SectionItem::where('section_id', $validatedData['data']['section-id'])->max('position') + 1;
                        $component->section_id = $validatedData['data']['section-id'];

                        if ( $component->save() ) {
                            return response("File component saved", 200);
                        } else {
                            return response("Couldn't make file component", 500);
                        }
                        break;
                    case "image":
                        // Validation for image
                        $imageValidation = Validator::make($validatedData, [
                            'data'       => [ 'required', 'array' ],
                            'data.image' => [ 'required', 'string', 'exists:course_files,id' ],
                            'data.alt'   => [ 'nullable', 'string' ],
                        ]);
                        if ( $imageValidation->fails() ) {
                            return response("Validation for image addition failed.", 403);
                        }
                        // Add the image component
                        $component = new SectionItem;
                        $component->title = "IMAGE_TITLE_NOT_SHOWN";
                        $component->item_type = "IMAGE";
                        $component->item_value = [ 'fileId' => $validatedData['data']['image'], 'alt' => $validatedData['data']['alt'] ?? 0 ];
                        $component->position = SectionItem::where('section_id', $validatedData['data']['section-id'])->max('position') + 1;
                        $component->section_id = $validatedData['data']['section-id'];

                        if ( $component->save() ) {
                            return response("Image course component saved successfully", 200);
                        } else {
                            return response("Unable to make image component", 500);
                        }
                        break;
                }
                break;
            case "section_item_delete":
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'         => [ 'required', 'array' ],
                    'data.item_id' => [ 'required', 'string', 'exists:section_items,id' ],
                ]);
                if ( $interiorValidation->fails() ) {
                    return response($interiorValidation->errors(), 403);
                }
                // Delete the item if validation is successful
                if ( SectionItem::where('id', $validatedData['data']['item_id'])->delete() ) {
                    return response("Section removal successful", 200);
                } else {
                    return response("Couldn't delete the section", 500);
                }
                break;
            case "section_item_move":
                // Further validation
                $interiorValidation = Validator::make($validatedData, [
                    'data'           => [ 'required', 'array' ],
                    'data.item_id'   => [ 'required', 'string', 'exists:section_items,id' ],
                    'data.direction' => [ 'required', 'string' ],
                ]);
                if ( $interiorValidation->fails() ) {
                    return response($interiorValidation->errors(), 400);
                }
                // Move the item if validation is successful
                $item = SectionItem::where('id', $validatedData['data']['item_id'])->firstOrFail();
                $section = Section::where('id', $item->section_id)->firstOrFail();
                if ( $validatedData['data']['direction'] === 'down' ) {
                    $newSectionId = Section::where('position', '>', $section->position)->min('position');
                } else {
                    $newSectionId = Section::where('position', '<', $section->position)->max('position');
                }
                $newSection = Section::where('position', $newSectionId)->firstOrFail();
                $updateSuccess = $item->update([
                    'section_id' => $newSection->id,
                    'position'   => $newSection->items->max('position') + 1,
                ]);
                // Attempt to move the new item
                if ( $updateSuccess ) {
                    return response("Move successful", 200);
                } else {
                    return response("Move unsuccessful", 500);
                }
            case "section_edit":
                // Further validation
                $interiorValidation = Validator::make($request->toArray(), [
                    'title'       => [ 'required', 'string' ],
                    'description' => [ 'nullable', 'string' ],
                    'section_id'  => [ 'required', 'string', 'exists:sections,id' ],
                    'course_id'   => [ 'required', 'string', 'exists:courses,id' ],
                ]);
                if ( $interiorValidation->fails() ) { // Redirect them BACK if there are validation errors
                    return back()->withErrors($interiorValidation->errors());
                }
                // Get the section
                $section = Section::where('id', $request->section_id)->firstOrFail();
                // Make the changes necessary
                $sectionUpdated = $section->update([
                    'title'       => $request->title,
                    'description' => $request->description ?? null,
                ]);
                // Save the changes
                if ( $sectionUpdated ) {
                    return redirect()->to(url()->previous() . '#' . $section->id);
                } else {
                    return back()->withErrors([ 'SAVE_FAILED' => 'Could not save the new section details.' ]);
                }
                break;
            default:
                return response("Could not make the requested edit type", 403);
        }
        return response("Could not make the requested edit type", 403);
    }

    /**
     * This DELETE route will allow the user to delete their course (after confirming their password).
     *
     * @param Request $request The HTTP request provided by laravel
     * @param string  $id      The course's id (UUID)
     *
     * @return RedirectResponse The route to redirect to during or after execution (depending on errors)
     */
    public function delete ( Request $request, string $id )
    {
        $course = CourseModel::find($id);
        if ( !Gate::allows('course-edit', $course) ) { // Protectively ensure the user can make edits if a new route is defined.
            return redirect()->to(route('course.home', [ 'id' => $course->id ]))->withErrors([ 'NO_EDIT_PERMISSION' => "You cannot complete this action as you do not own this course." ]);
        }
        // Lessons are independent of courses, so delete them first
        DB::beginTransaction();
        try {
            foreach ( $course->sections as $section ) {
                foreach ( $section->items as $item ) {
                    if ( $item->item_type == 'LESSON' ) {
                        $lesson = Lesson::whereId($item->item_value['lesson_id'])->firstOrFail();
                        $lesson->delete();
                    }
                }
            }
            DB::commit();
        } catch ( \Exception $exception ) {
            DB::rollBack();
            return back()->withErrors([ 'LESSON_DELETE_ERROR' => 'Could not delete all the course lessons. Please try again.' ]);
        }
        // Delete the course after lessons are done
        if ( $course->delete() ) {
            return redirect()->to(route('home'));
        } else {
            return redirect()->to(route('course.home', [ 'id' => $id ]))->withErrors([ 'DELETE_ERROR' => 'Could not delete the course, please try again!' ]);
        }
    }

}
