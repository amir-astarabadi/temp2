<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_happy_path(): void
    {
        Notification::fake();
        $userData = [
            'name' => fake()->firstName(),
            'email' => 'example@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'accepted_contract' => true,
        ];
        $response = $this->postJson(route('user_register'), $userData);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $assertableJson) use ($userData) {
            $assertableJson
                ->has('data')
                ->has('data.token')
                ->where('data.email', $userData['email'])
                ->where('data.name', $userData['name'])
                ->etc();
        });

        Notification::assertSentTo(User::where('email', $userData['email'])->first(), VerifyEmailNotification::class);
    }

    public function test_duplicate_email_not_permitted(): void
    {
        $user = User::factory()->create();
        $userData = [
            'name' => fake()->firstName(),
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'accepted_contract' => true,
        ];
        $response = $this->postJson(route('user_register'), $userData);

        $response->assertUnprocessable();
        $response->assertJson(function (AssertableJson $assertableJson) use ($userData) {
            $assertableJson
                ->where('message', 'The email has already been taken. Please singin.')
                ->etc();
        });
    }
}
