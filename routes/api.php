<?php

use App\Http\Controllers\Authentication\GoogleLoginController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('health-check', fn() => ['message' => 'I am ok.']);

Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:3,1');
Route::post('register', [AuthController::class, 'register'])->name('user_register')->middleware('throttle:1,1');

Route::prefix('google')->group(function () {
    Route::get('/login', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::prefix('password')->middleware('throttle:2,1')->group(function () {
    Route::post('forget', [AuthController::class, 'passwordForget'])
        ->name('password_forget')
        ->middleware('guest');

    Route::post('reset', [AuthController::class, 'passwordReset'])
        ->name('password_reset');
});

Route::get('verify/{user}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:2,1'])
    ->name('email_verification');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/user/verify', [AuthController::class, 'resendVerifyEmail'])
        ->middleware('throttle:2,1')
        ->name('user.verify');

    Route::apiResource('projects', ProjectController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->middleware('throttle:10,1');

    Route::apiResource('datasets', DatasetController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->middleware('throttle:10,1');
});
