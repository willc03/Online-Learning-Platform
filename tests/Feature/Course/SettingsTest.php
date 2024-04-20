<?php

namespace Tests\Feature\Course;

use App\Models\CourseInvite;
use App\Models\User;
use App\Models\UserCourse;
use Faker\Provider\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{

    use RefreshDatabase;

    /**
     * An array that contains the default invite information.
     */
    private array $sampleInviteDetails = [
        'active'      => 'on',
        'allowedUses' => 10,
        'neverExpire' => 'on',
    ];

    /**
     * A test to ensure unauthenticated users can't access a course's settings page
     */
    public function test_reject_unauthenticated_access_to_course_settings ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Try to access the settings page
        $response = $this->get('/course/' . $course->id . '/settings');

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/login');
        $response->assertSessionHas([ 'url.intended' ]);
    }

    /**
     * A test to ensure non-members can't access a course's settings page
     */
    public function test_reject_non_member_access_to_course_settings ()
    {
        // Login
        $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Try to access the settings page
        $response = $this->get('/course/' . $course->id . '/settings');

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure non-owners can't access a course's settings page
     */
    public function test_reject_non_owner_access_to_course_settings ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Create a course user record
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to access the settings page
        $response = $this->get('/course/' . $course->id . '/settings');

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
    }

    /**
     * A test to check the allowance of owner access to the settings page
     */
    public function test_allow_owner_access_to_course_settings ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Try to access the settings page
        $response = $this->actingAs($courseOwnerRecord)->get('/course/' . $course->id . '/settings');

        // Assert the necessary responses
        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    /**
     * A test to check unauthorised persons cannot post setting changes. We assume
     * from the previous tests that non-members and unauthorised users cannot
     * access these pages.
     */
    public function test_reject_non_owner_setting_changes ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Create a course user record
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to apply the changes
        $response = $this->post('/course/' . $course->id . '/settings', [
            'title'       => 'A brand new course title!',
            'description' => 'An all-new description!',
        ]);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
        $this->assertDatabaseMissing('courses', [ 'title' => 'A brand new course title!', 'description' => 'An all-new description!', 'id' => $course->id ]);
    }

    /**
     * A test to check the allowance of course owners to
     * make changes to course settings
     */
    public function test_allow_owner_setting_changes ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Try to apply the changes
        $response = $this->actingAs($courseOwnerRecord)->post('/course/' . $course->id . '/settings', [
            'title'       => 'A brand new course title!',
            'description' => 'An all-new description!',
        ]);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id . '/settings');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('courses', [ 'title' => 'A brand new course title!', 'description' => 'An all-new description!', 'id' => $course->id ]);
    }

    /**
     * A test to ensure non-owners cannot create invitations.
     */
    public function test_reject_new_invite_requests_from_non_owners ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Create a course user record
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to apply the changes
        $response = $this->post('/course/' . $course->id . '/settings/invite/new', $this->sampleInviteDetails);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure owners can create invitations.
     */
    public function test_allow_new_invite_requests_from_owners ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Try to apply the changes
        $response = $this->actingAs($courseOwnerRecord)->post('/course/' . $course->id . '/settings/invite/new', $this->sampleInviteDetails);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id . '/settings');
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure non-owners cannot edit invitations
     */
    public function test_reject_invite_edits_from_non_owners ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Create a course user record
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Create an invite record
        $invite = new CourseInvite;
        $invite->id = CourseInvite::all()->count() + 1;
        $invite->invite_id = Uuid::uuid();
        $invite->course_id = $course->id;
        $invite->expiry_date = null;
        $invite->max_uses = null;
        $invite->is_active = true;
        $invite->save();

        // Try to apply the changes
        $response = $this->post('/course/' . $course->id . '/settings/invite', [
            'inviteId'         => $invite->invite_id,
            'modificationType' => 'activeState',
        ]);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('course_invites', [ 'id' => $invite->id, 'is_active' => true ]);
    }

    /**
     * A test to ensure owners CAN edit invitations
     */
    public function test_accept_invite_edits_from_owners ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Create an invitation record
        $invite = new CourseInvite;
        $invite->id = CourseInvite::all()->count() + 1;
        $invite->invite_id = Uuid::uuid();
        $invite->course_id = $course->id;
        $invite->expiry_date = null;
        $invite->max_uses = null;
        $invite->is_active = true;
        $invite->save();

        // Try to apply the changes
        $response = $this->actingAs($courseOwnerRecord)->post('/course/' . $course->id . '/settings/invite', [
            'inviteId'         => $invite->invite_id,
            'modificationType' => 'activeState',
        ]);

        // Assert the necessary responses
        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('course_invites', [ 'id' => $invite->id, 'is_active' => false ]);
    }

    /**
     * A test to ensure non-owners cannot delete invitations
     */
    public function test_reject_invite_delete_requests_from_non_owners ()
    {
        // Login
        $user = $this->loginWithFakeUser();
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Create a course user record
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Create an invite record
        $invite = new CourseInvite;
        $invite->id = CourseInvite::all()->count() + 1;
        $invite->invite_id = Uuid::uuid();
        $invite->course_id = $course->id;
        $invite->expiry_date = null;
        $invite->max_uses = null;
        $invite->is_active = true;
        $invite->save();

        // Try to apply the changes
        $response = $this->delete('/course/' . $course->id . '/settings/invite', [
            'inviteId' => $invite->invite_id,
        ]);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('course_invites', [ 'id' => $invite->id ]);
    }

    /**
     * A test to ensure owners CAN delete invitations
     */
    public function test_allow_owners_to_delete_invites ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Create an invite record
        $invite = new CourseInvite;
        $invite->id = CourseInvite::all()->count() + 1;
        $invite->invite_id = Uuid::uuid();
        $invite->course_id = $course->id;
        $invite->expiry_date = null;
        $invite->max_uses = null;
        $invite->is_active = true;
        $invite->save();

        // Try to apply the changes
        $response = $this->actingAs($courseOwnerRecord)->delete('/course/' . $course->id . '/settings/invite', [
            'inviteId' => $invite->invite_id,
        ]);

        // Assert the necessary responses
        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertDatabaseMissing('course_invites', [ 'id' => $invite->id ]);
    }

    /**
     * A test to ensure non-owners can't remove users
     */
    public function test_reject_non_owner_requests_to_remove_users ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Add the first user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Generate another user and add them to the course
        $secondUser = User::factory()->create();
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $secondUser->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to apply the changes
        $response = $this->delete('/course/' . $course->id . '/settings/user/delete', [
            'userId' => $secondUser->id,
        ]);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('user_courses', [ 'id' => $userCourseRecord->id ]);
    }

    /**
     * A test to ensure owners CAN remove users
     */
    public function test_allow_owners_to_remove_users ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Generate a user and add them to the course
        $secondUser = User::factory()->create();
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $secondUser->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to apply the changes
        $response = $this->actingAs($courseOwnerRecord)->delete('/course/' . $course->id . '/settings/user/delete', [
            'userId' => $userCourseRecord->id,
        ]);

        // Assert the necessary responses
        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertDatabaseMissing('user_courses', [ 'id' => $userCourseRecord->id ]);
    }

    /**
     * A test to ensure non-owners can't remove users
     */
    public function test_reject_non_owner_requests_to_block_users ()
    {
        // Login
        $user = $this->loginWithFakeUser();

        // Fetch a course for testing with
        $course = $this->generate_course();

        // Add the first user to the course
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $user->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Generate another user and add them to the course
        $secondUser = User::factory()->create();
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $secondUser->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to apply the changes
        $response = $this->post('/course/' . $course->id . '/settings/user/block', [
            'userId' => $secondUser->id,
        ]);

        // Assert the necessary responses
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $course->id);
        $response->assertSessionHasErrors([ "NOT_COURSE_OWNER" ]);
        $this->assertAuthenticated();
        $this->assertDatabaseMissing('user_courses', [ 'id' => $userCourseRecord->id, 'blocked' => 1 ]);
    }

    /**
     * A test to ensure owners CAN remove users
     */
    public function test_allow_owners_to_block_users ()
    {
        // Fetch a course for testing with
        $course = $this->generate_course();

        // Get the course owner
        $courseOwnerRecord = $course->course_owner;

        // Generate a user and add them to the course
        $secondUser = User::factory()->create();
        $userCourseRecord = new UserCourse;
        $userCourseRecord->id = UserCourse::count() + 1;
        $userCourseRecord->course_id = $course->id;
        $userCourseRecord->user_id = $secondUser->id;
        $userCourseRecord->blocked = false;
        $userCourseRecord->save();

        // Try to apply the changes
        $response = $this->actingAs($courseOwnerRecord)->post('/course/' . $course->id . '/settings/user/block', [
            'userId' => $userCourseRecord->id,
        ]);

        // Assert the necessary responses
        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('user_courses', [ 'id' => $userCourseRecord->id, 'blocked' => true ]);
    }

}
