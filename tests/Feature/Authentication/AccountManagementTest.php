<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{

    use RefreshDatabase;

    /**
     * A feature test to reject unauthenticated users from viewing the accounts page
     */
    public function test_redirect_unauthenticated_users_from_management_page () : void
    {
        $response = $this->get('/account');

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertRedirect('/login');
        $response->assertSessionHas([ 'url.intended' ]);
    }

    /**
     * A feature test to show authenticated users their account management page
     */
    public function test_allow_authenticated_user_to_view_account () : void
    {
        $this->loginWithFakeUser();

        $response = $this->get('/account');

        $response->assertStatus(200);
    }

    /**
     * A feature test to reject password changes if the current password is wrong
     */
    public function test_reject_password_change_request_if_password_is_wrong () : void
    {
        $this->loginWithFakeUser();

        $response = $this->post('/account/new-password', [
            'current-password'          => "AnIncorrectPa55word",
            'new-password'              => "MyNewPa55word!",
            'new-password_confirmation' => "MyNewPa55word!",
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'WRONG_PASSWORD' ]);
    }

    /**
     * A feature test to reject password changes if the new password doesn't meet the requirements
     */
    public function test_reject_password_change_request_if_password_does_not_meet_requirements () : void
    {
        $this->loginWithFakeUser();

        $response = $this->post('/account/new-password', [
            'current-password'          => User::factory()->testPassword,
            'new-password'              => "MyNewPa55word",
            'new-password_confirmation' => "MyNewPa55word",
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'new-password' ]);
    }

    /**
     * A feature test to reject password changes if the new password confirmation is not
     * correct.
     */
    public function test_reject_password_change_request_if_password_confirm_is_wrong () : void
    {
        $this->loginWithFakeUser();

        $response = $this->post('/account/new-password', [
            'current-password'          => User::factory()->testPassword,
            'new-password'              => "MyNewPa55word!",
            'new-password_confirmation' => "MyNewPa55word?",
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors([ 'new-password' ]);
    }

    /**
     * A feature test to accept password changes if the new password confirmation is
     * correct.
     */
    public function test_accept_valid_password_change_request () : void
    {
        // Log in with a fake user
        $user = $this->loginWithFakeUser();

        // Make a request to change the password
        $response = $this->post('/account/new-password', [
            'current-password'          => User::factory()->testPassword,
            'new-password'              => "MyNewPa55word!",
            'new-password_confirmation' => "MyNewPa55word!",
        ]);

        // Assert the response status and redirection
        $response->assertStatus(302);
        $response->assertRedirect();

        // Verify that the hashed password has been updated
        $user->refresh();
        $this->assertTrue(Hash::check("MyNewPa55word!", $user->password));
    }

    /**
     * A test for testing password.confirm
     */
    public function test_delete_account_request_requires_password_confirmation () : void
    {
        // Login with a fake user
        $user = $this->loginWithFakeUser();
        $userId = $user->id;

        // Make a request to delete the account
        $response = $this->followingRedirects()->delete('/account/delete', [
            'password' => User::factory()->testPassword,
        ]);

        // Assertions
        $response->assertStatus(200);
        $response->assertSee("Password Confirmation");
    }

}
