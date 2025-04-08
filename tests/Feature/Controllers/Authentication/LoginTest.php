<?php

namespace Tests\Feature\Controllers\Authentication;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Http\Response;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{

    public function test_happy_path(): void
    {
        User::factory()->create(['email' => $email = 'example@example.com', $password = 'password' => 'password']);
        $response = $this->postJson(route('login'), ['email' => $email, 'password' => $password]);

        $response->assertOk();
        $response->assertJson(function (AssertableJson $assertableJson) {
            $assertableJson
                ->has('data')
                ->has('data.token')
                ->etc();
        });
    }

    public function test_invalid_credentials_get_401(): void
    {
        User::factory()->create(['email' => 'example@example.com', 'password' => 'password']);
        $response = $this->postJson(route('login'), ['email' => 'wrong_email@gmail.com', 'password' => 'wrong password']);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
