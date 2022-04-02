<?php

namespace Tests\Feature\Request\Traits;

use App\Models\Role;
use App\Models\User;

trait ActingAsRole
{
    private User $currentUserWithRole;

    private function actingAsRole($role) : User
    {
        $user = User::factory()->has(Role::factory()
            ->state(function (array $attributes, User $user) use ($role) {
                return ['user_id' => $user->id, 'type' => $role];
            }))
            ->createOne();
        $this->actingAs($user);
        return $user;
    }
}
