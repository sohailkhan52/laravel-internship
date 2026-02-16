<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * API Login (JWT)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }
         // Token TTL in minutes
         $ttl = auth('api')->factory()->getTTL();

         // Expiry timestamp
         $expiresAt = now()->addMinutes($ttl);

         // Get authenticated user
         $user = auth('api')->user();

         // Update token info in DB
         $user->update([
             'token' => $token,
             'token_expires' => $expiresAt,
         ]);

        return response()->json([
            'status' => 'success',
            'user'   => auth('api')->user(),
            'authorization' => [
                'token'       => $token,
                'type'        => 'bearer',
                'expires_in'  => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * API Register (JWT)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            
        ]);

        $token = auth('api')->login($user);
    $ttl = auth('api')->factory()->getTTL();

    $expiresAt = now()->addMinutes($ttl);

    $user->update([
        'token' => $token,
        'token_expires' => $expiresAt,
    ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ], 201);
    }

    /**
     * Get authenticated API user
     */
    public function user()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'authorization' => [
                'token' => auth('api')->refresh(),
                'type'  => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * Logout API user
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
}
