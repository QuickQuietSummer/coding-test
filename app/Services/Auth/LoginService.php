<?php


namespace App\Services\Auth;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    private TokenIssuer $tokenIssuer;

    public function __construct(TokenIssuer $tokenIssuer)
    {
        $this->tokenIssuer = $tokenIssuer;
    }

    public function login($email, $password): string
    {
        $user = User::whereEmail($email)->firstOr(function () {
            return abort(404, 'Account not found');
        });
        if (Hash::check($password, $user->password) === false) {
            return abort(401, 'Wrong credentials');
        }

        $this->tokenIssuer->revokeAllTokens($user);

        return $this->tokenIssuer->createToken($user);
    }
}
