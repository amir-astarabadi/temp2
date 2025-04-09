<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendVerifyEmailTest extends TestCase
{
    public function test_happy_path(): void
    {
        Notification::fake();
        $this->login();

        $this->postJson(route('user.verify'));

        Notification::assertSentTo($this->authUser, VerifyEmailNotification::class);
    }

    public function test_guest_user_cannot_request_for_resending_email(): void
    {
        $response = $this->postJson(route('user.verify'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
