<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('admin123'), 
            'role' => 'admin',
        ]);
    }
}
