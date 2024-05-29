<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'string | required | max:255',
            'email' => 'string | required | max:255 | email | unique:users',
            'password' => 'min:6 | required | string ',
        ]);

        //create the user profile

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return response()->json($user, 201);
    }
    public function login(Request $request)
    {
         $request->validate([
            'email' => 'string | required |email ',
            'password' => ' required | string ',
        ]);
        $credentials = $request->only('email', 'password');

        //create the user profile
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => ' Invalid Credentials'
            ]);
        };
        $user = $request->user();
        $token = $user()->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
