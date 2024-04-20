<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A test that ensures users can view the registration page
     */
    public function test_login_page_available () : void
    {
        // Get the page
        $response = $this->get('/login');

        // Test the response
        $response->assertStatus(200);
    }

    /**
     * A test that ensures users can log in to the application with the correct username and password
     *
     * @return void
     */
    public function test_login_successful_with_correct_password () : void
    {
        // Define an artificial user to complete tests with
        $user = User::factory()->create();

        // Send the POST request
        $response = $this->post('/login', [ 'username' => $user->username, 'password' => User::factory()->testPassword ]);

        // Ensure responses are as intended
        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    /**
     * A test that ensures users are sent back with an error message if the password is wrong
     *
     * @return void
     */
    public function test_login_fail_with_incorrect_password () : void
    {
        // Define an artificial user to complete tests with
        $user = User::factory()->create();

        // Send the POST request
        $response = $this->post('/login', [ 'username' => $user->username, 'password' => 'IncorrectPassword123' ]);

        // Ensure responses are as intended
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([ 'INVALID_PASSWORD' ]);
    }

    /**
     * A test that ensures users are sent back with an error message if the username is invalid
     *
     * @return void
     */
    public function test_login_fail_with_invalid_username () : void
    {
        $artificalUsername = "Art!fic4lU5sern4me";
        // Send the POST request
        $response = $this->post('/login', [ 'username' => $artificalUsername, 'password' => 'IncorrectPassword123' ]);

        // Ensure responses are as intended
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([ 'USERNAME_NOT_FOUND' ]);
        $this->assertDatabaseMissing('users', [ 'username' => $artificalUsername ]);
    }

}
