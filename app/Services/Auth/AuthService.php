<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $token = $user->createToken($user->email . '-auth_token')->plainTextToken;
        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Invalid login credentials', 401);
        }

        $user = Auth::user();
        $token = $user->createToken($user->email . '-auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
