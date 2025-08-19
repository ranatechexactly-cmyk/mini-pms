<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        // Hash the password
        $data['password'] = Hash::make($data['password']);
        // Create the user
        $user = User::create($data);
        // Create API token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->toISOString()
            ],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /**
     * Check if email already exists
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Get available roles
     *
     * @return array
     */
    public function getAvailableRoles(): array
    {
        return ['Developer', 'Admin', 'Manager', 'User'];
    }
}
