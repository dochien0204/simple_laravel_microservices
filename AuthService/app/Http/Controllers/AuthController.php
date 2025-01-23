<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function createJwtToken(User $user)
    {
        $payload = [
            'iss' => 'user-service',
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return $jwt;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $jwt = $this->createJwtToken($user);

        return response()->json(['token' => $jwt]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $jwt = $this->createJwtToken($user);

        return response()->json(['token' => $jwt]);
    }

    public function healthCheck()
    {
        return response()->json(['data' => 'success'], 200);
    }
}
