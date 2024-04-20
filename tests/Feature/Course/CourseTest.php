<?php

namespace Tests\Feature\Course;

use App\Models\Section;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A test to ensure users are correctly redirected if they are not logged in
     */
    public function test_reject_course_access_without_authentication () : void
    {
        // Get a course to test with
        $course = $this->generate_course();

        // Try to access the course
        $response = $this->get('/course/' . $course->id);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/login');
        $response->assertSessionHas([ 'url.intended' ]);
    }

    /**
     * A test to ensure users are correctly redirected if the course does
     * not exist
     */
    public function test_redirect_if_course_does_not_exist () : void
    {
        $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Try to access the course
        $response = $this->get('/course/' . $course->id . 'bad_appendix');

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([ 'INVALID_COURSE' ]);
    }

    /**
     * A test to ensure users are correctly redirected if they are not a course
     * member.
     */
    public function test_reject_course_access_if_user_is_not_course_member () : void
    {
        $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Try to access the course
        $response = $this->get('/course/' . $course->id);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([ 'NOT_COURSE_MEMBER' ]);
    }

    /**
     * A test to ensure users are correctly redirected if they are blocked from
     * the course.
     */
    public function test_reject_course_access_if_user_is_blocked () : void
    {
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Generate a record to block the user from the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = true;
        $userCourseRecord->save();

        // Try to access the course
        $response = $this->get('/course/' . $course->id);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([ 'BLOCKED' ]);
    }

    /**
     * A test to ensure users are can view the course if they have permission
     */
    public function test_allow_course_access_if_user_is_has_permission () : void
    {
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Generate a record of the user being on the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to access the course
        $response = $this->get('/course/' . $course->id);

        // Assert the necessary responses
        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure users are able to leave courses should they wish to.
     */
    public function test_allow_course_member_to_leave_course () : void
    {
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Generate a record of the user being on the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to access the course
        $response = $this->delete('/course/' . $course->id . '/leave');

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure users are redirected and informed if they try to
     * leave a course they are not a member of
     */
    public function test_redirect_non_members_who_send_a_leave_request () : void
    {
        $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Try to access the course
        $response = $this->delete('/course/' . $course->id . '/leave');

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([ 'NOT_COURSE_MEMBER' ]);
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure users who are not the owner cannot edit the course.
     */
    public function test_reject_edit_requests_from_non_owners () : void
    {
        $user = $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Generate a record of the user being on the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Edit request details
        $editRequestContents = [
            'edit_type' => 'new_section',
            'course_id' => $course->id,
            'data'      => Json::encode([ [ 'name' => 'title', 'value' => 'A new test course section' ], [ 'name' => 'description', 'value' => 'An invigorating description!' ] ]),
        ];

        // Try to access the course
        $response = $this->post('/course/' . $course->id . '/edit', $editRequestContents);

        // Assert the necessary responses
        $response->assertStatus(302); // The middleware will take over as a redirect even though
        $this->assertAuthenticated();
        $this->assertDatabaseMissing('sections', [ 'title' => 'A new test course section', 'description' => 'An invigorating description!' ]);
    }

    /**
     * A test to ensure users who are not the owner cannot edit the course.
     */
    public function test_allow_edit_requests_from_owners () : void
    {
        // Get a course to test with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = User::find($course->owner);

        // Edit request details
        $editRequestContents = [
            'edit_type' => 'new_section',
            'course_id' => $course->id,
            'data'      => Json::encode([ [ 'name' => 'title', 'value' => 'A new test course section' ], [ 'name' => 'description', 'value' => 'An invigorating description!' ] ]),
        ];

        // Try to access the course
        $response = $this->actingAs($courseOwnerRecord)->post('/course/' . $course->id . '/edit', $editRequestContents);

        // Assert the necessary responses
        $response->assertStatus(200); // The middleware will take over as a redirect even though
        $this->assertAuthenticated();
        $this->assertDatabaseHas('sections', [ 'title' => 'A new test course section', 'description' => 'An invigorating description!' ]);
    }

    /**
     * A test to ensure form requests are rejected from non-owners
     */
    public function test_reject_non_owner_form_requests () : void
    {
        $this->loginWithFakeUser();

        // Get a course to test with
        $course = $this->generate_course();

        // Add a section to test with
        $section = new Section;
        $section->title = "A test section";
        $section->course_id = $course->id;
        $section->position = 1;
        $section->save();

        // Request the form
        $response = $this->get('/course/' . $course->id . '/formRequest', [
            'form_type'  => 'lesson',
            'section_id' => $section->id,
            'course_id'  => $course->id,
        ]);

        // Test the response
        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
    }

    /**
     * A test to ensure form requests are accepted from owners
     */
    public function test_accept_owner_form_requests () : void
    {
        // Get a course to test with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = User::find($course->owner);

        // Add a section to test with
        $section = new Section;
        $section->title = "A test section";
        $section->course_id = $course->id;
        $section->position = 1;
        $section->save();

        // Request the form
        $response = $this->actingAs($courseOwnerRecord)->get('/course/' . $course->id . '/formRequest', [
            'form_type'  => 'lesson',
            'section_id' => $section->id,
            'course_id'  => $course->id,
        ]);

        // Test the response
        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
    }

}
