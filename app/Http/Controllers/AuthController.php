<?php

namespace App\Http\Controllers;

use App\Models\TemporaryUser;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
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

        $verification_code = rand(100000, 999999);

        try {
            Mail::to($request->email)->send(new VerificationCodeMail($verification_code));
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to send verification email.'], 500);
        }

        $temporaryUser = TemporaryUser::where('email', $request->email)->first();
        if ($temporaryUser) {
            $temporaryUser->update([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => Hash::make($request->password),
                'verification_code' => $verification_code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);
        } else {
            TemporaryUser::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'verification_code' => $verification_code,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);
        }

        return response()->json(['message' => 'Verification code sent to your email.', 'status'=>201], 201);

    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'verification_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $temporaryUser = TemporaryUser::where('email', $request->email)
        ->where('verification_code', $request->verification_code)
        ->where('expires_at', '>', Carbon::now())
        ->first();

        if (!$temporaryUser) {
            return response()->json(['message' => 'Invalid verification code or email.'], 400);
        }

        $user = User::create([
            'username' => $temporaryUser->username,
            'first_name' => $temporaryUser->first_name,
            'last_name' => $temporaryUser->last_name,
            'email' => $temporaryUser->email,
            'password' => $temporaryUser->password,
            'email_verified_at' => Carbon::now(),  // Update email_verified_at
        ]);

        $temporaryUser->delete();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 201,
            'authorisation' => [
                'token' => $token,
                'user' => $user
            ],'data' => [
                'user' => $user,
                'role' => $user->role,
            ]
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:username|string|email',
            'username' => 'required_without:email|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid credentials','status'=>422], 422);
        }

        if (count($request->all()) > 2) {
            return response()->json(['message' => 'Wrong input', 'status'=>422], 422);
        }

        $credentials = $request->only('email', 'username', 'password');

        if ($request->has('email')) {
            $credentials = $request->only('email', 'password');
        } else {
            $credentials = ['username' => $request->username, 'password' => $request->password];
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials', 'status'=> 401], 401);
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            'status' => 200,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
            'data' => [
                'user' => $user,
                'role' => $user->role,
            ]
        ])->header('Authorization', 'Bearer ' . $token); // Set token in the headers
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'User not found', 'status' => 404], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'user' => $user,
                'role' => $user->role,
            ]
        ]);
    }

    public function validateToken(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $token = JWTAuth::getToken();

            // Optionally refresh the token if needed
            $newToken = JWTAuth::refresh($token);

            return response()->json([
                'status' => 200,
                'authorisation' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                ],
                'data' => [
                    'user' => $user,
                    'role' => $user->role,
                ]
            ])->header('Authorization', 'Bearer ' . $newToken);
        } catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
            return response()->json(['message' => 'Token is blacklisted'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        }
    }

    public function refresh()
{
    return response()->json([
        'status' => 'success',
        'user' => Auth::user(),
        'authorisation' => [
            'token' => Auth::refresh(),
            'type' => 'bearer',
        ]
    ]);
}

}
