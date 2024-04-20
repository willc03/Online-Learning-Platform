<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A test to ensure users can access the course page while unauthenticated
     *
     * @return void
     */
    public function test_course_browsing_page_available_while_unauthenticated () : void
    {
        // Fetch the page
        $response = $this->get('/courses');

        // Assert the necessary information
        $response->assertStatus(200);
    }

    /**
     * A test to ensure users can access the course page when authenticated
     *
     * @return void
     */
    public function test_course_browsing_page_available_when_authenticated () : void
    {
        $this->loginWithFakeUser();

        // Fetch the page
        $response = $this->get('/courses');

        // Assert the necessary information
        $response->assertStatus(200);
        $this->assertAuthenticated();
    }

    /**
     * A test to ensure course creation requests are rejected if the user is not authenticated.
     *
     * @return void
     */
    public function test_reject_course_creation_request_if_unauthenticated () : void
    {
        // Send the POST request
        $response = $this->post('/course/new', $this->sampleCourseDetails);

        // Make sure the request is rejected
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHas([ 'url.intended' ]);
    }

    /**
     * A test to ensure course creation requests are ACCEPTED if the user is logged in.
     *
     * @return void
     */
    public function test_create_course_if_user_is_logged_in () : void
    {
        $this->loginWithFakeUser();

        // Send the POST request
        $response = $this->post('/course/new', $this->sampleCourseDetails);

        // Retrieve the course
        $courseRecord = Course::where(array_diff_key($this->sampleCourseDetails, array_flip([ 'publicity' ])))->firstOrFail();

        // Make sure the request is rejected
        $this->assertAuthenticated();
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/course/' . $courseRecord->id);
        $this->assertDatabaseHas('courses', array_diff_key($this->sampleCourseDetails, array_flip([ 'publicity' ])));
    }

}
