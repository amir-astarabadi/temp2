<?php

namespace App\Http\Controllers\Authentication;

use App\Services\Authentication\AuthService;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\GoogleLoginRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Responses\Response;
use Illuminate\Support\Str;
use App\Models\User;

class GoogleLoginController extends Controller
{
    public function __construct(private AuthService $authService) {}


    public function handleGoogleCallback(GoogleLoginRequest $reqquest)
    {
        $user = User::where('email', $reqquest->validated('email'))->first();

        if (!$user) {
            $userData = [
                'name' => $reqquest->validated('name'),
                'email' => $reqquest->validated('email'),
                'password' => Hash::make(Str::random(16))
            ];

            $user = $this->authService->register(userData: $userData, verifyEmail: true);
        } else {
            $this->authService->logout($user);
            $user->token = $this->authService->login($user);
        }

        if ($user->email_verified_at === null) {
            $user->markEmailAsVerified();
        }

        return Response::success('Login successful', UserResource::make($user), code: 200);
    }
}
