<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class MainTest extends TestCase
{
    /**
     * A test redirect to login from home page.
     *
     * @return void
     */
    public function test_redirect_to_login_from_home_page()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * A test redirect to login from home page.
     *
     * @return void
     */
    public function test_redirect_to_login_from_bla_bla_page()
    {
        $response = $this->get('/bla-bla');

        $response->assertRedirect('/login');
    }

    /**
     * A test redirect to login from home page.
     *
     * @return void
     */
    public function test_authenticated_users_can_access_home_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
