<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'string | required | max:255',
                'email' => 'string | required | max:255 | email ',
                'password' => 'min:6 | required | string ',

            ]
        );
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 400);
        }

        //create the user profile

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]);
    }
    public function login(Request $request)
    {
        $validatedData = Validator::make(
            $request->all(),
            [
                'email' => ' required |email ',
                'password' => ' required | string ',

            ]
        );
        if ($validatedData->fails()) {
            return response()->json($validatedData->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
