<?php

namespace App\Services\Authentication;

use App\Exceptions\Authentication\LoginException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use App\Models\User;

class AuthService
{
    public function login(array $credentials): string
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw new LoginException(code: Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new LoginException(code: Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return $token;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function register(array $userData): User
    {
        $user = new User();
        $user->accepted_contract = $userData['accepted_contract'] ?? false;
        $user->email = $userData['email'] ?? '';
        $user->password = Hash::make($userData['password'] ?? '');
        $user->name = $userData['name'] ?? '';
        $user->save();
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return $user;
    }

    public function createForgetPasswordResetToken(User $user)
    {
        return app('auth.password.broker')->createToken($user);
    }

    public function resetPassword(string $password, User $user):void
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
