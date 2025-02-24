<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    // a list of all users is displayed
    public function index()
    {
        try {
            $users = User::paginate(10);
            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error: ' . $e->getMessage()
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    //a new task is created
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            // confirmed verifies that the password field matches another field called password_confirmation
            'password' => 'required|string|min:8|confirmed'
        ]);
        if($validator->fails()) {
            return response()->json(
                [
                    'errors' => $validator->errors()
                ], 400
            );
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // hash here the password is encrypted with hash
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'message' => 'User created successfully!',
            'user' => $user
        ], 201);
    }
}
