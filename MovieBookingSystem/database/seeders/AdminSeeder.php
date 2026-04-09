<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'Admin Moovy',
            'email' => 'admin@moovy.com',
            'password' => bcrypt('admin123'), 
            'role' => 'admin',
        ]);
    }
}
