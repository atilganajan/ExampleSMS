<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->only("username", "password");

            User::create([
                "username" => $data["username"],
                "password" => bcrypt($data["password"])
            ]);

            return response()->json(['message' => 'User successfully registered'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $request->only(["username", "password"]);

            if (!$token = auth()->attempt($data)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return $this->createNewToken($token);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error'], 500);
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'User logged out']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unexpected error'], 500);
        }
    }

    public function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }



}
