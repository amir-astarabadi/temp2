<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_happy_path(): void
    {
        $userData = [
            'accepted_contract' => true,
            'email' => 'example@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->postJson(route('user_register'), $userData);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $assertableJson) use ($userData) {
            $assertableJson
                ->has('data')
                ->has('data.token')
                ->where('data.email', $userData['email'])
                ->etc();
        });
    }

    public function test_duplicate_email_not_permitted(): void
    {
        $user = User::factory()->create();
        $userData = [
            'accepted_contract' => true,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->postJson(route('user_register'), $userData);
        
        $response->assertUnprocessable();
        $response->assertJson(function (AssertableJson $assertableJson) use ($userData) {
            $assertableJson
                ->where('message', 'The email has already been taken.')
                ->etc();
        });
    }
}
