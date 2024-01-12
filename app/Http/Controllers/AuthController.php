<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'email'=> 'required|string|email',
            'password'=> 'required|string'
        ]);

        // check validator
        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed',
                'errors'=> $validator->errors()
            ], 422);
        }

        // get the credentials
        $credentials = $request->only(['email', 'password']);

        // check the credentials
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message'=> 'Unauthorized: Invalid credentials',
            ], 401);
        }

        return response()->json([
            'message'=> 'Successfully logged in',
            'token'=> $token,
            'user'=> auth('api')->user()
        ], 200);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string',
            'email'=> 'required|string|email|unique:users',
            'password'=> 'required|string|confirmed'
        ]);

        // check validator
        if ($validator->fails()) {
            return response()->json([
                'message'=> 'Validation failed',
                'errors'=> $validator->errors()
            ], 422);
        }


        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password)
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'message'=> 'User successfully registered',
            'token'=> $token,
            'user'=> $user
        ], 201);
    }
}
