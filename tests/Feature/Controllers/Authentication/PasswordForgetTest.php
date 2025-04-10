<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Models\User;
use App\Notifications\ForgetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PasswordForgetTest extends TestCase
{
    public function setUp() : void 
    {
        parent::setUp();
        Notification::fake();

    }
    
    public function test_happy_path_when_no_user_exists_with_requested_email(): void
    {
        $userData = [
            'email' => 'example@gmail.com',
        ];
        
        $response = $this->postJson(route('password_forget'), $userData);
        
        $response->assertOk();
        $response->assertJson(function (AssertableJson $assertableJson) use ($userData) {
            $assertableJson
                ->where('message', 'Forgent password link sent to ' . $userData['email'])
                ->etc();
        });
        Notification::assertNothingSent();
    }
    
    public function test_happy_path_when_user_exists_with_requested_email(): void
    {
        $user = User::factory()->create();
        $userData = [
            'email' => $user->email,
        ];
        
        $response = $this->postJson(route('password_forget'), $userData);
        
        $response->assertOk();
        $response->assertJson(function (AssertableJson $assertableJson) use ($userData) {
            $assertableJson
            ->where('message', 'Forgent password link sent to ' . $userData['email'])
            ->etc();
        });
        Notification::assertSentTo($user, ForgetPasswordNotification::class);
    }
}
