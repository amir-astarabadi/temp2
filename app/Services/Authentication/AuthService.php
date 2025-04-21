<?php

namespace App\Services\Authentication;

use App\Exceptions\Authentication\LoginException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use App\Models\User;

class AuthService
{
    public function login(User|array $user): string
    {
        if ($user instanceof User) {
            return $user->createToken('auth_token')->plainTextToken;
        }

        $condidatUser = User::where('email', $user['email'])->first();

        if (!$condidatUser) {
            throw new LoginException(code: Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($user['password'], $condidatUser->password)) {
            throw new LoginException(code: Response::HTTP_UNAUTHORIZED);
        }

        return $condidatUser->createToken('auth_token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function register(array $userData, bool $verifyEmail = false, bool $withToken = true): User
    {
        $user = new User();
        $user->accepted_contract = $userData['accepted_contract'] ?? false;
        $user->email = $userData['email'] ?? '';
        $user->password = Hash::make($userData['password'] ?? '');
        $user->name = $userData['name'] ?? '';

        if ($verifyEmail) {
            $user->markEmailAsVerified();
        };
        $user->save();

        if ($withToken) {
            $user->token = $user->createToken('auth_token')->plainTextToken;
        }

        return $user;
    }

    public function createForgetPasswordResetToken(User $user)
    {
        return app('auth.password.broker')->createToken($user);
    }

    public function resetPassword(string $password, User $user): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
