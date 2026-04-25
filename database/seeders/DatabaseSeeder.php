<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed movies data
        $this->call([
            AdminSeeder::class,
            MovieSeeder::class,
            OfferSeeder::class,
        ]);
    }
}
