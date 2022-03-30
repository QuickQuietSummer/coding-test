<?php

namespace Database\Seeders;

use App\Models\Bid;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Bid::factory(30)->create();
    }
}
