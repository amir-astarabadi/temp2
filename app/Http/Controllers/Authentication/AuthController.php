<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\PasswordForgetRequest;
use App\Http\Requests\Authentication\PasswordResetRequest;
use App\Http\Requests\Authentication\UserRegisterRequest;
use App\Http\Requests\Authentication\VerifyEmailRequest;
use App\Exceptions\Authentication\LoginException;
use App\Http\Requests\Authentication\LoginRequest;
use App\Notifications\ForgetPasswordNotification;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Authentication\AuthService;
use App\Http\Resources\User\UserResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $userData = $request->only('name', 'email', 'password', 'accepted_contract');
        $user = $this->authService->register($userData);

        $user->sendEmailVerificationNotification();

        return Response::success('Registration successful', UserResource::make($user), code: 201);
    }

    public function verifyEmail(User $user, VerifyEmailRequest $request)
    {
        $hash = $request->get('token');
        $expireAt = $request->get('expire_at');

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash) || now()->timestamp > $expireAt) {
            return Response::error('Invalid verification link', code: HttpResponse::HTTP_UNAUTHORIZED);
        }

        $user->markEmailAsVerified();

        return Response::success('Email verified successfully', UserResource::make($user));
    }

    public function resendVerifyEmail()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->sendEmailVerificationNotification();

        return Response::success("Verification email sent to {$user->email}. Please check your inbox.", [], code: HttpResponse::HTTP_OK);
    }

    public function passwordForget(PasswordForgetRequest $request)
    {
        $user = User::where('email', $request->validated('email'))->first();

        $message = 'Forget password link sent to ' . $request->validated('email');

        if ($user) {

            $token = $this->authService->createForgetPasswordResetToken($user);
            $user->notify(new ForgetPasswordNotification($token, $user->email));
        }

        return Response::success($message);
    }

    public function passwordReset(PasswordResetRequest $request)
    {
        $user = auth()->user();
        $record = null;
   
        if (!$user) {

            $record = DB::table('password_resets')
                ->where('email', $request->validated('email'))
                ->first();

            $user = User::where('email', $record?->email)->first();
        }

        if (!$user && !Hash::check($request->validated('token'), $record?->token)) {
            return Response::error('Invalid or expired token', code: HttpResponse::HTTP_UNAUTHORIZED);
        }

        $this->authService->resetPassword($request->validated('password'), $user);
        $this->authService->logout($user);
        $user->token = $this->authService->login($user);

        
        return Response::success('Password reset successfully', UserResource::make($user));
    }
}
