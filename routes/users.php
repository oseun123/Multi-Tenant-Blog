<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserAuthController;


Route::prefix('user')->group(function () {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);
    Route::post('forgot-password', [UserAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [UserAuthController::class, 'resetPassword']);
});

// Protected routes (for logged-in users)
Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::get('profile', [UserAuthController::class, 'profile']);
    Route::post('logout', [UserAuthController::class, 'logout']);
});
