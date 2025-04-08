<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Models\User;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_happy_path(): void
    {
        $user = User::factory()->create();
        $this->login($user);
               
        $response = $this->postJson(route('logout'));

        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
    }
}
