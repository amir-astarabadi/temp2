<?php

use App\Http\Controllers\Authentication\GoogleLoginController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('health-check', fn() => ['message' => 'I am ok.']);

Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:3,1');
Route::post('register', [AuthController::class, 'register'])->name('user_register')->middleware('throttle:1,1');

Route::post('google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::prefix('password')->middleware('throttle:2,1')->group(function () {
    Route::post('forget', [AuthController::class, 'passwordForget'])
        ->name('password_forget')
        ->middleware('guest');

    Route::post('reset', [AuthController::class, 'passwordReset'])
        ->name('password_reset');
});

Route::post('verify/{user}', [AuthController::class, 'verifyEmail'])
    ->middleware(['throttle:2,1'])
    ->name('email_verification');

Route::middleware(['auth:sanctum', 'throttle:3,1'])->group(function () {
    Route::post('email-verification/resend', [AuthController::class, 'resendVerifyEmail'])->name('resend_eerify_email');
});

Route::middleware(['auth:sanctum', 'verified_email'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/user/verify', [AuthController::class, 'resendVerifyEmail'])
        ->middleware('throttle:2,1')
        ->name('user.verify');

    Route::apiResource('projects', ProjectController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->middleware('throttle:10,1');

    Route::apiResource('datasets', DatasetController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware('throttle:10,1');
    Route::put('datasets/{dataset}/pin', [DatasetController::class, 'pin'])
        ->middleware('throttle:3,1')
        ->name('datasets.pin');
    Route::put('datasets/{dataset}/unpin', [DatasetController::class, 'unpin'])
        ->middleware('throttle:2,1')
        ->name('datasets.unpin');
    Route::get('datasets/{dataset}/metadata', [DatasetController::class, 'metadata'])
        ->middleware('throttle:10,1')
        ->name('datasets.metadata');

    Route::post('datasets/{dataset}/charts', [ChartController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('charts.store');

    Route::get('profile', [ProfileController::class, 'show'])
        ->name('profiles.show')
        ->middleware('throttle:10,1');
    Route::delete('users', [UserController::class, 'destroy'])->name('users.delete');
});


Route::get('test/{dataset}', [DatasetController::class, 'test']);
