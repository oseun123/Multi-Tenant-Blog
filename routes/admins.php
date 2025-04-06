<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserApprovalController;

Route::prefix('admin')->group(function () {

    // Auth routes

    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AdminAuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout']);

        Route::get('pending-users', [UserApprovalController::class, 'pendingUsers']);
        Route::post('approve-user/{id}', [UserApprovalController::class, 'approveUser']);
    });
});
