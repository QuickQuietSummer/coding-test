<?php


namespace App\Services\Auth;


use App\Models\User;

class TokenIssuer
{
    public function createToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
