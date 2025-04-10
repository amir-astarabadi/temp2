<?php

namespace Tests\Feature\Controllers\Authentication;

use Illuminate\Testing\Fluent\AssertableJson;
use App\Services\Authentication\AuthService;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{    
    public function test_happy_path_with_auth_user(): void
    {
        $this->login();
        $userData = [
            'email' => $this->authUser->email,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ];
        
        $response = $this->postJson(route('password_reset'), $userData);
       
       
        $response->assertOk(); 
        $this->assertTrue(Hash::check($userData['password'], $this->authUser->refresh()->password));
        $response->assertJson(function (AssertableJson $assertableJson) {
            $assertableJson
            ->where('message', 'Password reset successfully')
            ->etc();
        });
    }
    
    public function test_happy_path_with_guest_user(): void
    {
        $user = User::factory()->create();
        $userData = [
            'email' => $user->email,
            'token' => resolve(AuthService::class)->createForgetPasswordResetToken($user),
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ];
        
        $response = $this->postJson(route('password_reset'), $userData);
        
        $response->assertOk(); 
        $this->assertTrue(Hash::check($userData['password'], $user->refresh()->password));
        $response->assertJson(function (AssertableJson $assertableJson) {
            $assertableJson
            ->where('message', 'Password reset successfully')
            ->etc();
        });
    }
}
