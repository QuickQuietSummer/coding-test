<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class RegisterService
{
    public function registerClient(string $name, string $email, string $password): string
    {
        $user = $this->register($name, $email, $password);
        Role::create(['user_id' => $user->id, 'type' => Role::CLIENT]);
        return $user->createToken('auth_token')->plainTextToken;
    }

    private function register(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public function registerEmployee(string $name, string $email, string $password): string
    {
        $user = $this->register($name, $email, $password);
        Role::create(['user_id' => $user->id, 'type' => Role::EMPLOYEE]);
        return $user->createToken('auth_token', ['create-request'])->plainTextToken;
    }


}
