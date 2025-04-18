<?php

namespace Tests\Feature\Controllers\Profile;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProfileShowTest extends TestCase
{
    public function test_happy_path(): void
    {
        $this->login();

        $response = $this->getJson(route('profiles.show'));

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->has('data')
                ->where('data.name', $this->authUser->name)
                ->where('data.email', $this->authUser->email)
                ->has('data.email_verified_at')
                ->etc()
        );
    }
}
