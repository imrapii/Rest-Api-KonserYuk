<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required','string','max:255','unique:users,username'],
            'email' => ['required','string','max:255','unique:users,email'],
            'password' => ['required','string','min:8','max:255','confirmed']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'data not match with our validation',
                'data' => $validator->errors()
            ], 422);
        }

        $validated = $validator->getData();

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => 'Data Berhasil Dibuat',
            'data' => $user
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required','string','max:255'],
            'password' => ['required','string','min:8','max:255']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status' => 'error',
                'message' => 'data not match with our validation',
                'data' => $validator->errors()
            ], 422);
        }

        $validated = $validator->getData();

        $user = User::where('username', $validated['username'])->get()->first();

        if (!$user) {
            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'user data not found'
            ], 401);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'password yang anda masukkan salah'
            ], 401);
        }

        $token = $user->createToken('sanctum_token')->plainTextToken;

        return response()->json([
            'code' => 201,
            'status' => 'success',
            'message' => $user->username . ' Berhasil Sign In',
            'data' => [
                'user' => [
                    'username' => $user->username,
                    'email' => $user->email
                ],
                'token' => $token,
                'token_type' => 'bearer'
            ]
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = auth('sanctum')->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'code' => 202,
            'status' => 'success',
            'message' => $user->username . ' Berhasil Sign Out',
            'data' => [
                'user' => [
                    'username' => $user->username,
                    'email' => $user->email
                ],
                'token' => 'null',
                'token_type' => 'null'
            ]
        ], 202);
    }
}
