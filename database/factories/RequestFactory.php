<?php

namespace Database\Factories;

use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $resolved = rand(true, false);

        return [
            'user_id' => 1234,
            'status' => $resolved ? Request::STATUS_RESOLVED : Request::STATUS_ACTIVE,
            'message' => $this->faker->text,
            'comment' => $resolved ? 'Comment text. Comment text.' : '',
            'created_at' => $this->faker->date
        ];
    }
}
