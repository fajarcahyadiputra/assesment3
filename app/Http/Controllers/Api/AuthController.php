<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'detail_address' => $request->detail_address,
                'city' => $request->detail_address,
                'country' => $request->country,
                'pastal_code' => $request->pas_code,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if(!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid Credentials',
                ], 401);
            }

            $user = auth()->user();
            return response()->json([
                'message' => 'successfully',
                'user' => $user,
                'token' => $token,
                'token_type' => "Bearer"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
