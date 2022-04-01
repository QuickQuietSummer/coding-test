<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class RegisterService
{
    private TokenIssuer $tokenIssuer;

    public function __construct(TokenIssuer $tokenIssuer)
    {
        $this->tokenIssuer = $tokenIssuer;
    }

    public function registerClient(string $name, string $email, string $password): string
    {
        $user = $this->register($name, $email, $password);
        Role::create(['user_id' => $user->id, 'type' => Role::CLIENT]);
        return $this->tokenIssuer->createToken($user);
    }

    public function registerEmployee(string $name, string $email, string $password): string
    {
        $user = $this->register($name, $email, $password);
        Role::create(['user_id' => $user->id, 'type' => Role::EMPLOYEE]);
        return $this->tokenIssuer->createToken($user);
    }

    private function register(string $name, string $email, string $password): User
    {
        if (User::whereEmail($email)->exists()) return abort(422, 'Email already exists');
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
