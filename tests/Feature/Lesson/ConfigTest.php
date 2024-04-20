<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\UserCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psy\Util\Json;
use Tests\TestCase;

class ConfigTest extends TestCase
{

    use RefreshDatabase;

    private function generate_lesson ( Course $course ) : Lesson
    {
        $lesson = new Lesson;
        $lesson->title = "A testing lesson";

        $section = new Section;
        $section->title = "A testing section";
        $section->position = 1;
        $section->course_id = $course->id;
        $section->save();

        $lesson_container = new SectionItem;
        $lesson_container->title = "A testing lesson";
        $lesson_container->position = 1;
        $lesson_container->section_id = $section->id;
        $lesson_container->item_type = 'LESSON';
        $lesson_container->item_value = [ 'lesson_id' => $lesson->id ];
        $lesson_container->save();

        $lesson->section_item_id = $lesson_container->id;
        $lesson->save();

        return $lesson;
    }

    /**
     * A test to ensure non-members cannot access the lesson configuration page
     */
    public function test_ensure_non_members_are_redirected_from_lesson_configuration () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Set up a user
        $user = $this->loginWithFakeUser();

        // Attempt to access the lesson configuration page
        $response = $this->get('/course/' . $course->id . '/lesson/' . $lesson->id . '/config');

        // Assertions
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NOT_COURSE_MEMBER' ]);
    }

    /**
     * A test to ensure non-OWNERS cannot access the lesson configuration page
     */
    public function test_ensure_non_owners_are_redirected_from_lesson_configuration () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Set up a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson configuration page
        $response = $this->get('/course/' . $course->id . '/lesson/' . $lesson->id . '/config');

        // Assertions
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NO_EDIT_PERMISSION' ]); // The gate occurs first h
    }

    /**
     * A test to ensure owners can access the lesson configuration page
     */
    public function test_allow_course_owner_config_access () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Attempt to access the lesson configuration page
        $response = $this->actingAs($course->course_owner)->get('/course/' . $course->id . '/lesson/' . $lesson->id . '/config');

        // Assertions
        $response->assertStatus(200);
    }

    /**
     * A test to ensure owners can add new items
     */
    public function test_allow_course_owner_to_add_new_lesson_items () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Attempt to access the lesson configuration page
        $response = $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/lesson/' . $lesson->id . '/config/add', [
            'item-type'                 => 'question',
            'item-title'                => 'A brand new question!',
            'description'               => 'An invigorating description!',
            'item-sub-type'             => 'single-choice',
            'item-answers'              => Json::encode([ [ 'answer' => 1, 'isCorrect' => true ], [ 'answer' => 2, 'isCorrect' => false ], [ 'answer' => 3, 'isCorrect' => false ], [ 'answer' => 4, 'isCorrect' => false ] ]),
            'item-allow-answer-changes' => true,
        ]);

        // Assertions
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id . '/lesson/' . $lesson->id . '/config');
    }

    /**
     * A test to ensure owners can modify the lesson order
     */
    public function test_allow_course_owner_to_edit_lesson_order () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add a wordsearch sample question
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            "question_type"    => "wordsearch",
            "words"            => [
                [ "Variable", "A memory location that stores data" ],
                [ "Function", "A block of code used to execute the same process repeatedly" ],
                [ "Object", "An instance of a class" ],
                [ "Class", "A template container for an object" ],
                [ "Integer", "A variable used to store whole numbers" ],
                [ "Float", "A variable used to store numbers with decimal places" ],
            ],
            "are_sides_random" => true,
        ];
        $lesson_item->save();

        // Add a single choice item
        $lesson_item2 = new LessonItem;
        $lesson_item2->item_title = "What is the value of three squared?";
        $lesson_item2->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item2->item_type = "QUESTION";
        $lesson_item2->position = 2;
        $lesson_item2->lesson_id = $lesson->id;
        $lesson_item2->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item2->save();

        // Attempt to access the lesson configuration page
        $response = $this->actingAs($course->course_owner)->post('/course/' . $course->id . '/lesson/' . $lesson->id . '/config/modify', [
            'edit-type' => 'order',
            'data'      => [
                [ 'id' => $lesson_item->id, 'position' => 2 ],
                [ 'id' => $lesson_item2->id, 'position' => 1 ],
            ],
        ]);

        // Assertions
        $response->assertStatus(200);
        $lesson_item->refresh();
        $lesson_item2->refresh();
        $this->assertDatabaseHas('lesson_items', [ 'id' => $lesson_item->id, 'position' => 2 ]);
        $this->assertDatabaseHas('lesson_items', [ 'id' => $lesson_item2->id, 'position' => 1 ]);
    }

    /**
     * A test to ensure owners can delete lesson components
     */
    public function test_allow_course_owner_to_delete_components () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add a wordsearch sample question
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            "question_type"    => "wordsearch",
            "words"            => [
                [ "Variable", "A memory location that stores data" ],
                [ "Function", "A block of code used to execute the same process repeatedly" ],
                [ "Object", "An instance of a class" ],
                [ "Class", "A template container for an object" ],
                [ "Integer", "A variable used to store whole numbers" ],
                [ "Float", "A variable used to store numbers with decimal places" ],
            ],
            "are_sides_random" => true,
        ];
        $lesson_item->save();
        $lesson_item_id = $lesson_item->id;

        // Attempt to access the lesson configuration page
        $response = $this->actingAs($course->course_owner)->delete('/course/' . $course->id . '/lesson/' . $lesson->id . '/config/modify', [
            'edit-type' => 'component-delete',
            'data'      => $lesson_item->id,
        ]);

        // Assertions
        $response->assertStatus(200);
        $this->assertDatabaseMissing('lesson_items', [ 'id' => $lesson_item->id ]);
    }

    /**
     * A test to ensure owners can request forms during config
     */
    public function test_allow_course_owner_to_request_forms () : void
    {
        // Set up a course and a lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Attempt to access the lesson configuration page
        $response = $this->actingAs($course->course_owner)->get(( '/course/' . $course->id . '/lesson/' . $lesson->id . '/config/form-request?form-name=text' ));

        // Assertions
        $response->assertStatus(200);
    }

}
