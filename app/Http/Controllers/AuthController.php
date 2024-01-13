<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function login(LoginRequest $request)
    {
        // get the credentials
        $credentials = $request->validated();

        //TODO: check later
        // check the credentials
        // if (!$token = auth('api')->attempt($credentials)) {
        //     return response()->json([
        //         'message'=> 'Unauthorized: Invalid credentials',
        //     ], 401);
        // }

        $user = User::where('email', $request->email)->first();
        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message'=> 'Successfully logged in',
            'token'=> $token,
            'data'=> auth()->user()
        ], 200);

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
            'message'=> 'Successfully retrieved user',
            'data'=> auth('api')->user()
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'message'=> 'Successfully logged out',
            'data'=> []
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password)
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'message'=> 'User successfully registered',
            'token'=> $token,
            'data'=> $user
        ], 201);
    }
}
