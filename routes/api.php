<?php

use App\Http\Controllers\Authentication\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('health-check', fn() => ['message' => 'I am ok.']);

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('user_register');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});