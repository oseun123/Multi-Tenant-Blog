<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;



Route::prefix('admin')->group(function () {

    // Auth routes

    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('logout', [AdminAuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
