<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed movies data
        $this->call([
            MovieSeeder::class,
            OfferSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
