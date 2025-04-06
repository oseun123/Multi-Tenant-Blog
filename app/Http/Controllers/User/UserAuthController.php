<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255|unique:users',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'status'   => 'pending',
        ]);

        return ResponseHelper::withSuccess('Registration successful. Awaiting admin approval.', [
            'user' => $user,
        ]);
    }


    public function login(Request $request)
    {

        // return $request;
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ResponseHelper::withError('Invalid credentials.', [], 401);
        }

        if ($user->status !== 'approved') {
            return ResponseHelper::withError('Account is pending approval by an admin.', [], 403);
        }

        $token = $user->createToken('User-Token')->plainTextToken;

        return ResponseHelper::withSuccess('Login successful', [
            'token' => $token,
            'user'  => $user,
        ]);
    }
}
