<?php

use App\Http\Controllers\Authentication\GoogleLoginController;
use Illuminate\Support\Facades\Route;

Route::prefix('google')->group(function () {
    Route::get('/login', [GoogleLoginController::class,  'redirectToGoogle'])->name('google.redirect');
});