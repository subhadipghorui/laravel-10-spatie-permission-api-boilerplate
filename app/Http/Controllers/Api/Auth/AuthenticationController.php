<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    // Add middleware 
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);//login, register methods won't go through the api guard
    }
    public function register(Request $request)
    {
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function getaccount()
    {
        return response()->json(auth()->user());
    }

    
    public function logout()
    {
        auth()->logout();

        // Pass true to force the token to be blacklisted "forever"
        auth()->logout(true);

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function refresh()
    {
        // $newToken = auth()->refresh();

        // Pass true as the first param to force the token to be blacklisted "forever".
        // The second parameter will reset the claims for the new token
        $newToken = auth()->refresh(true, true);
        return $this->respondWithToken($newToken);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 //mention the guard name inside the auth fn
        ]);
    }
}
