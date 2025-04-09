<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    protected ?User $authUser = null;

    public function login(?User $user = null): void
    {
        $this->authUser = $user;

        if ($this->authUser === null) {
            $this->authUser = User::factory()->create();
        }

        Sanctum::actingAs($this->authUser, ['*']);
    }
}
