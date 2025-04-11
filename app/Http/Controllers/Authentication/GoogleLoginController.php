<?php

namespace App\Http\Controllers\Authentication;

use App\Services\Authentication\AuthService;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class GoogleLoginController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->email)->first();

        if (!$user) {
            $userData = [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(16))
            ];

            $user = $this->authService->register(userData: $userData, verifyEmail: true);
        }

        $queryParams =  "?" . http_build_query([
            'message' => 'Logedin successful',
            'name' => $user->name,
            'email' => $user->email,
            'token' => $this->authService->login($user)
        ]);

        return redirect()->to(config('auth.frontend_home_url') . $queryParams);
    }
}
