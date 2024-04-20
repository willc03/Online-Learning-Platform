<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaticTest extends TestCase
{

    /**
     * Test that the welcome page is correctly delivered
     */
    public function test_welcome_page_is_available () : void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test that the welcome page is correctly delivered
     */
    public function test_home_url_redirects_to_welcome () : void
    {
        $response = $this->get('/home');

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /**
     * Test that the welcome page is correctly delivered
     */
    public function test_about_page_is_available () : void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }

}
