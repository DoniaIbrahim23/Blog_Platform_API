<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,author', // فقط اللي مطلوبين
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // Create token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        return response()->json([
            'message' => 'Logged in successfully.',
            'token'   => $token,
            'user'    => auth()->user()
        ]);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'message' => 'Token refreshed successfully.',
            'token'   => $newToken
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
}
