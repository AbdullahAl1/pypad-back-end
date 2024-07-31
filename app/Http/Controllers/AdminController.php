<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    public function addAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(), // Assuming admin verification is immediate
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 201,
            'message' => 'Admin created successfully',
            'data' => [
                'token' => $token,
                'user' => $user
            ]
        ], 201);
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => Carbon::now(), // Assuming admin verification is immediate
        ]);


        return response()->json([
            'status' => 201,
            'message' => 'user created successfully',
            'data' => [
                'user' => $user
            ]
        ], 201);
    }


    public function removeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user->role == 'admin') {
            $user->role = 'user';
            $user->save();
            return response()->json(['message' => 'Admin removed successfully', 'status' => 200], 200);
        }

        return response()->json(['message' => 'User is not an admin', 'status' => 400], 400);
    }

    public function viewUsers()
    {
        $users = User::where('role', 'user')->get();
        return response()->json(['status' => 200, 'data' => $users], 200);
    }

    public function viewAdmins()
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'superadmin')->get();
        return response()->json(['status' => 200, 'data' => $admins], 200);
    }
}
