<?php

namespace Tests\Feature\Request\Traits;

use App\Models\Role;
use App\Models\User;

trait ActingAsRole
{
    private function actingAsRole($role)
    {
        $user = User::factory()->has(Role::factory()
            ->state(function (array $attributes, User $user) use ($role) {
                return ['user_id' => $user->id, 'type' => $role];
            }))
            ->createOne();
        $this->actingAs($user);
    }
}
