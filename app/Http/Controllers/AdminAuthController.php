<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Helpers\ResponseHelper;
use App\Notifications\AdminPasswordResetNotification;

class AdminAuthController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return ResponseHelper::withSuccess('Admin registered successfully.', $admin);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return ResponseHelper::withError('Invalid credentials.', [], 401);
        }

        $token = $admin->createToken('Admin-API-Token')->plainTextToken;

        return ResponseHelper::withSuccess('Login successful!', [
            'token' => $token,
            'admin' => $admin,
        ]);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        $token = Str::random(64);

        $admin->update([
            'reset_token' => $token,
            'reset_expires_at' => now()->addHour(), // Token expires in 1 hour
        ]);

        $admin->notify(new AdminPasswordResetNotification($token));

        return ResponseHelper::withSuccess('A password reset link has been sent to your email.');
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::where('email', $request->email)
            ->where('reset_token', $request->reset_token)
            ->first();

        if (!$admin || ($admin->reset_expires_at && now()->gt($admin->reset_expires_at))) {
            return ResponseHelper::withError('Invalid or expired reset token.');
        }

        $admin->update([
            'password' => Hash::make($request->password),
            'reset_token' => null,
            'reset_expires_at' => null,
        ]);

        return ResponseHelper::withSuccess('Password reset successful. You can now log in.');
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ResponseHelper::withSuccess('Logout successful.');
    }
}
