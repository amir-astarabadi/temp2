<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\UserRegisterRequest;
use App\Exceptions\Authentication\LoginException;
use App\Http\Requests\Authentication\LoginRequest;
use App\Services\Authentication\AuthService;
use App\Http\Controllers\Controller;
use App\Responses\Response;

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
    
        return Response::success('Registration successful', [
            'token' => $user->token,
            'email' => $user->email,
        ], code: 201);
    }
}
