<?php

namespace Database\Seeders;

use App\Models\Request;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\TokenIssuer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(TokenIssuer $tokenIssuer)
    {
        User::factory(30)
            ->has(
                Role::factory()
                    ->state(function (array $attributes, User $user) {
                        return ['user_id' => $user->id];
                    }))
            ->has(
                Request::factory()
                    ->state(function (array $attributes, User $user) {
                        return ['user_id' => $user->id];
                    }))
            ->create()
            ->each(function (User $user) use ($tokenIssuer) {
                $tokenIssuer->createToken($user);
            });
    }
}
