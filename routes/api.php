<?php

use App\Http\Controllers\Authentication\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('health-check', fn() => ['message' => 'I am ok.']);

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('user_register');
Route::post('password/forget', [AuthController::class, 'passwordForget'])->name('password_forget');
Route::get('password/reset', [AuthController::class, 'passwordResetCheck'])->name('password_reset_check');
Route::post('password/reset', [AuthController::class, 'passwordReset'])->name('password_reset');
Route::get('verify/{user}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:2,1'])
    ->name('email_verification');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/user/verify', [AuthController::class, 'resendVerifyEmail'])
        ->middleware('throttle:2,1')
        ->name('user.verify');
});