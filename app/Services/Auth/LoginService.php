<?php


namespace App\Services\Auth;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    /**
     * Return token string if ok
     * @return string
     */
    public function login($email, $password): string
    {
        $user = User::whereEmail($email)->wherePassword(Hash::make($password))
            ->firstOr(function () {
                abort(401);
            });
        return $user->tokens;
    }
}
