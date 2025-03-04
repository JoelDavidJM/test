<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Users register here
    public function register(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            // With confirmed you have to confirm the password
            'password' => 'required|string|min:8|confirmed'
        ]);
        // create user
        $user = User::create([
            'name' => $validator['name'],
            'email' => $validator['email'],
            'password' => bcrypt($validator['password'])
        ]);

        return response()->json(
            [
                'message' => 'User register successfully'
            ],
            201
        );
    }

    // Here users log in with email and password
    // login user
    public function login(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8'
        ]);
        // Auth::attempt() to verify credentials
        if (Auth::attempt(['email' => $validator['email'], 'password' => $validator['password']])) {
            // Auth::user(); It is to verify data that was created
            $user = Auth::user();
            // createToken() generates a token for the authenticated user.
            // plainTextToken gets the token in plain text
            $token = $user->createToken('AuthToken')->plainTextToken;
            return response()->json(
                [
                    'token' => $token
                ]
            );
        }
        return response()->json(
            [
                'message' => 'invalid credential'
            ],401
        );
    }

     //logout
     public function logout(Request $request) {
        // $request->user()->tokens returns the authenticated user
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });
        return response()->json(['message' => 'logged out successfully']);
    }
}
