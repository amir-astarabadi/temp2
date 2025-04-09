<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\UserRegisterRequest;
use App\Exceptions\Authentication\LoginException;
use App\Http\Requests\Authentication\LoginRequest;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Authentication\AuthService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Responses\Response;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = $this->authService->login($credentials);
            return Response::success('Login successful', ['token' => $token], code: 200);
        } catch (LoginException $e) {
            return Response::error(message: $e->getMessage(), code: 401);
        }
    }

    public function logout()
    {
        $this->authService->logout(auth()->user());

        return Response::success('Logged out successfully');
    }

    public function register(UserRegisterRequest $request)
    {
        $userData = $request->only('name', 'email', 'password');
        $user = $this->authService->register($userData);

        $user->sendEmailVerificationNotification();
    
        return Response::success('Registration successful', [
            'token' => $user->token,
            'email' => $user->email,
            'message' => "Verification email sent to {$user->email}. Please check your inbox.",
        ], code: 201);
    }

    public function verifyEmail(User $user, string $hash)
    {
        if (! hash_equals(sha1( $user->getEmailForVerification()), (string) $hash)) {
            return Response::error('Invalid verification link', code: 403);
        }

        if ($user->hasVerifiedEmail()) {
            return Response::error('Email already verified', code: 400);
        }

        $user->markEmailAsVerified();

        return Response::success('Email verified successfully');
    }

    public function resendVerifyEmail()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->sendEmailVerificationNotification();

        return Response::success("Verification email sent to {$user->email}. Please check your inbox.", [
        ], code: HttpResponse::HTTP_OK);

    }
    
}
