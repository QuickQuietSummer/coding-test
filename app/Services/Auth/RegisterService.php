<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        return $this->register($name, $email, $password, Role::CLIENT);
    }

    public function registerEmployee(string $name, string $email, string $password): string
    {
        return $this->register($name, $email, $password, Role::EMPLOYEE);
    }

    private function register(string $name, string $email, string $password, string $role): string
    {
        if (User::whereEmail($email)->exists()) {
            return abort(422, 'Email already exists');
        }
        return DB::transaction(function () use ($name, $email, $password, $role) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            Role::create(['user_id' => $user->id, 'type' => $role]);
            return $this->tokenIssuer->createToken($user);
        });
    }
}
