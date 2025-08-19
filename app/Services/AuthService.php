<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate user and generate token
     *
     * @param array $credentials
     * @return array
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.'
            ]);
        }

        $user = Auth::user();
        // Revoke existing tokens (single session)
        $user->tokens()->delete();
        // Create new Sanctum token
        $token = $user->createToken('api_token', ['*'])->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_login' => now()->toISOString()
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => null
        ];
    }

    /**
     * Logout user by revoking current token
     *
     * @param User $user
     * @return array
     */
    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();
        return [
            'message' => 'Token revoked successfully',
            'logged_out_at' => now()->toISOString()
        ];
    }

    /**
     * Get authenticated user profile
     *
     * @param User $user
     * @return array
     */
    public function getUserProfile(User $user): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->toISOString(),
                'tokens_count' => $user->tokens()->count()
            ]
        ];
    }
}
