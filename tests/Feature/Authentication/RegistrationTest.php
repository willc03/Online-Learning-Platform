<?php

namespace Tests\Feature\Authentication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{

    use RefreshDatabase;

    private array $artificialRegistrationForm = [
        'firstname'             => 'John',
        'lastname'              => 'Doe',
        'username'              => 'john.doe',
        'email'                 => 'john.doe@example.com',
        'password'              => 'd5tF<VqR)Rs!]=Ju',
        'password_confirmation' => 'd5tF<VqR)Rs!]=Ju',
    ];

    /**
     * A test that ensures users can view the registration page
     */
    public function test_registration_page_available () : void
    {
        // Get the page
        $response = $this->get('/register');

        // Test the response
        $response->assertStatus(200);
    }

    /**
     * A test that ensures users can register to the database using a form
     *
     * @return void
     */
    public function test_user_registered_to_database () : void
    {
        // Send the POST request
        $response = $this->post('/register', $this->artificialRegistrationForm);

        // Ensure responses are as intended
        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertAuthenticated();

        // Ensure the database contents exist
        $this->assertDatabaseHas('users', [ 'username' => $this->artificialRegistrationForm['username'] ]);
    }

    /**
     * A test that ensures users are redircted back if the registration form is not filled in
     * properly.
     *
     * @return void
     */
    public function test_user_redirected_if_invalid_form () : void
    {
        $artificialFormInfo = $this->artificialRegistrationForm;
        $artificialFormInfo['password'] = 'PasswordDoesNotMeetRequirements';
        // Send the POST request
        $response = $this->post('/register', $artificialFormInfo);

        // Ensure responses are as intended
        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors([ 'password' ]);

        // Ensure the database contents exist
        $this->assertDatabaseMissing('users', [ 'username' => $artificialFormInfo['username'] ]);
    }

}
