<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\UserCourse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A feature test to make sure the application dies non-owner access to lesson attempts
     */
    public function test_deny_non_owner_access_to_attempts () : void
    {
        // Create a course
        $course = $this->generate_course();

        // Create a lesson container
        $lesson = $this->generate_lesson($course);

        // Add a user to the course
        $user = $this->loginWithFakeUser();
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's attempts
        $response = $this->get('/course/' . $course->id . '/lesson/' . $lesson->id . '/attempts');

        $response->assertStatus(302);
        $response->assertRedirect();
    }

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
     * A feature test to make sure the application dies non-owner access to lesson attempts
     */
    public function test_allow_owner_access_to_attempts () : void
    {
        // Create a course
        $course = $this->generate_course();

        // Create a lesson container
        $lesson = $this->generate_lesson($course);

        // Attempt to access the lesson's attempts
        $response = $this->actingAs($course->course_owner)->get('/course/' . $course->id . '/lesson/' . $lesson->id . '/attempts');

        $response->assertStatus(200);
    }

    /**
     * Block non-member access to lesson pages
     */
    public function test_block_non_member_access_to_lesson_pages () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Create a user
        $user = $this->loginWithFakeUser();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');

        // Assertions
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NOT_COURSE_MEMBER' ]);
    }

    /**
     * Make sure users cannot take empty lessons
     */
    public function test_redirect_members_upon_empty_lessons () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');

        // Assertions
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NO_CONTENT' ]);
    }

    /**
     * Allow members to start lessons
     */
    public function test_allow_members_to_start_lessons () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item->save();

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');

        // Assertions
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);
    }

    /**
     * Allow members to view questions
     */
    public function test_member_can_view_questions () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item->save();

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);

        // Access the question
        session()->put('lesson.position', 1);
        $response->assertSessionHas('lesson.position', 1);

        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id);

        // Assertions
        $response->assertStatus(200);
        $response->assertSeeText($lesson_item->item_title);
    }

    /**
     * Allow members to answer questions
     */
    public function test_member_answer_incorrect () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item->save();

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);

        // Access the question
        session()->put('lesson.position', 1);
        $response->assertSessionHas('lesson.position', 1);

        // Check the user can view the questions
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id);
        $response->assertStatus(200);
        $response->assertSeeText($lesson_item->item_title);

        // Submit an answer
        $response = $this->post('/course/' . $course->id . "/lesson/" . $lesson->id . "/answer", [
            'question_id' => $lesson_item->id,
            'answer'      => 3,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'WRONG' ]);
    }

    /**
     * Allow members to answer questions correctly
     */
    public function test_member_answer_correct () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item->save();

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);

        // Access the question
        session()->put('lesson.position', 1);
        $response->assertSessionHas('lesson.position', 1);

        // Check the user can view the questions
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id);
        $response->assertStatus(200);
        $response->assertSeeText($lesson_item->item_title);

        // Submit an answer
        $response = $this->post('/course/' . $course->id . "/lesson/" . $lesson->id . "/answer", [
            'question_id' => $lesson_item->id,
            'answer'      => 9,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHas([ "COMPLETED_LESSON", "LESSON_TITLE" ]);
    }

    /**
     * Allow members to leave lessons
     */
    public function test_member_leaving_lesson () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item->save();

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);

        // Access the question
        session()->put('lesson.position', 1);
        $response->assertSessionHas('lesson.position', 1);

        // Check the user can view the questions
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id);
        $response->assertStatus(200);
        $response->assertSeeText($lesson_item->item_title);

        // Submit an answer
        $response = $this->post('/course/' . $course->id . "/lesson/" . $lesson->id . "/end");
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
    }

    /**
     * Expect an error if members try to leave lessons when not taking
     * one
     */
    public function test_error_presented_if_user_leaves_lesson_when_one_is_not_in_progress () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
        $lesson_item = new LessonItem;
        $lesson_item->item_title = "What is the value of three squared?";
        $lesson_item->description = "The square value of a number is calculated by multiplying the number by itself";
        $lesson_item->item_type = "QUESTION";
        $lesson_item->position = 1;
        $lesson_item->lesson_id = $lesson->id;
        $lesson_item->item_value = [
            'question_type'    => 'single_choice',
            "question_choices" => [ 3, 6, 9, 27 ],
            "correct_answer"   => 9,
            "one_time_answer"  => false,
        ];
        $lesson_item->save();

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Submit an answer
        $response = $this->post('/course/' . $course->id . "/lesson/" . $lesson->id . "/end");
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'NO_LESSON' ]);
    }

    /**
     * Check incorrect partial answers are correctly responded to
     */
    public function test_error_response_upon_incorrect_partial_answer () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
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

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);

        // Access the question
        session()->put('lesson.position', 1);
        $response->assertSessionHas('lesson.position', 1);

        // Check the user can view the questions
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id);
        $response->assertStatus(200);
        $response->assertSeeText($lesson_item->item_title);

        // Submit an answer
        $response = $this->post('/course/' . $course->id . "/lesson/" . $lesson->id . "/partial", [
            'question_id' => $lesson_item->id,
            'answer'      => [ 'AVeryRandomStringOfWords!' ],
        ]);
        $response->assertStatus(200);
        $response->assertSeeText('false');
    }

    /**
     * Check correct partial answers are correctly responded to
     */
    public function test_success_response_upon_correct_partial_answer () : void
    {
        // Create a course and lesson
        $course = $this->generate_course();
        $lesson = $this->generate_lesson($course);

        // Add one item to the lesson
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

        // Create a user
        $user = $this->loginWithFakeUser();

        // Join the user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Attempt to access the lesson's main page.
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id . '/start');
        $response->assertSessionHas([ 'lesson' => [
            'id'       => $lesson->id,
            'position' => -1,
            'streak'   => 1,
            'xp'       => 0,
            'answered' => [],
        ] ]);

        // Access the question
        session()->put('lesson.position', 1);
        $response->assertSessionHas('lesson.position', 1);

        // Check the user can view the questions
        $response = $this->get('/course/' . $course->id . "/lesson/" . $lesson->id);
        $response->assertStatus(200);
        $response->assertSeeText($lesson_item->item_title);

        // Submit an answer
        $response = $this->post('/course/' . $course->id . "/lesson/" . $lesson->id . "/partial", [
            'question_id' => $lesson_item->id,
            'answer'      => [ 'Variable' ],
        ]);
        $response->assertStatus(200);
        $response->assertJson([ 'Variable', 'A memory location that stores data' ]);
    }

}
