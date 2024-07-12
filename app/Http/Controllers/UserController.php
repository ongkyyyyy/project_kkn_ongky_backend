<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $user = User::where('username', $request->username)->first();

            if (!$user) throw new \Exception('Username tidak ditemukan');

            if (!password_verify($request->password, $user->password)) throw new \Exception('Password salah');

            return response()->json([
                'status' => true,
                'message' => 'Berhasil login',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }
}
