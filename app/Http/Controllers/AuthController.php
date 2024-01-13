<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{

   /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unexpected error")
     *         )
     *     )
     * )
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

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

       /**
        * Log in a user.
        *
        * @OA\Post(
        *     path="/api/login",
        *     summary="Log in a user",
        *     tags={"Authentication"},
        *     @OA\RequestBody(
        *         required=true,
        *         @OA\JsonContent(
        *             required={"username", "password"},
        *             @OA\Property(property="username", type="string"),
        *             @OA\Property(property="password", type="string"),
        *         ),
        *     ),
        *     @OA\Response(
        *         response=200,
        *         description="Access token generated successfully",
        *         @OA\JsonContent(
        *             @OA\Property(property="access_token", type="string", example="your_access_token"),
        *             @OA\Property(property="token_type", type="string", example="bearer"),
        *             @OA\Property(property="expires_in", type="integer", example=3600),
        *         )
        *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Unauthorized",
        *         @OA\JsonContent(
        *             @OA\Property(property="error", type="string", example="Unauthorized")
        *         )
        *     ),
        *     @OA\Response(
        *         response=500,
        *         description="Unexpected error",
        *         @OA\JsonContent(
        *             @OA\Property(property="error", type="string", example="Unexpected error")
        *         )
        *     )
        * )
        *
        * @param \App\Http\Requests\Auth\LoginRequest $request
        * @return \Illuminate\Http\JsonResponse
        */

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


    /**
     * @OA\POST(
     *     path="/api/logout",
     *     summary="Logout a user",
     *     tags={"Authentication"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unexpected error")
     *         )
     *     )
     * )
     *
     * @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuth",
     * )
     */


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
