<?php

namespace Tests;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;

    /**
     * An array that contains the basic details a course needs for use in testing
     *
     * @var array|string[]
     */
    protected array $sampleCourseDetails = [
        'title'       => 'Test course',
        'description' => 'A description for the test course',
        'publicity'   => 'on',
    ];

    /**
     * A protected function to generate a course for use in testing
     *
     * @return Course
     */
    protected function generate_course () : Course
    {
        $owner = User::factory()->create();

        $course = new Course;
        $course->title = $this->sampleCourseDetails['title'];
        $course->description = $this->sampleCourseDetails['description'];
        $course->is_public = isset($this->sampleCourseDetails['publicity']) ? 1 : 0;
        $course->owner = $owner->id;

        $course->save();
        return $course;
    }

    /**
     * A protected function to allow tests to utilise login facilities
     * with a fake user
     * @return User
     */
    protected function loginWithFakeUser ()
    {
        $user = User::factory()->create();
        $this->post('/login', [ 'username' => $user->username, 'password' => User::factory()->testPassword ]);

        return $user;
    }

}
