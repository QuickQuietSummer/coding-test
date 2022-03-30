<?php

namespace Database\Factories;

use App\Models\Bid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BidFactory extends Factory
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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'status' => $resolved ? Bid::STATUS_RESOLVED : Bid::STATUS_ACTIVE,
            'message' => $this->faker->text,
            'comment' => $resolved ? 'Comment text. Comment text.' : '',
            'created_at' => $this->faker->date
        ];
    }
}
