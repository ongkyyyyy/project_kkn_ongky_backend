<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenResult;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:6'
            ]);

            $user = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password'])
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil register',
                'data' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $request->username)->first();

            if (!$user) {
                throw new \Exception('Username tidak ditemukan');
            }

            if (!Hash::check($request->password, $user->password)) {
                throw new \Exception('Password salah');
            }

            $token = $user->createToken('Authentication Token')->accessToken;

            return response()->json([
                'status' => true,
                'message' => 'Berhasil login',
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 400);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil logout',
        ]);
    }
}
