<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $role = rand(true, false) ? Role::EMPLOYEE : Role::CLIENT;
        return [
            'user_id' => 1234,
            'type' => $role,
        ];
    }
}
