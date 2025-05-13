<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Authentication\AuthService;

class UserService
{
    public function __construct(private AuthService $authService)
    {}

    public function delete(User$user): void
    {
        $this->authService->logout($user);

        $user->delete();
    }
}
