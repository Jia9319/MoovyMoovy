<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cinema;

class CinemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        $cinemas = [
            [
                'name' => 'GSC Mid Valley',
                'location' => 'Mid Valley Megamall',
                'address' => 'Lingkaran Syed Putra',
                'city' => 'Kuala Lumpur',
                'state' => 'Kuala Lumpur',
                'postal_code' => '59200',
                'phone' => '03-1234 5678',
                'email' => 'midvalley@gsc.com.my',
                'description' => 'Experience ultimate cinema with IMAX and D-BOX seats at GSC Mid Valley.',
                'facilities' => 'IMAX,D-BOX,HALAL Snacks,Premium Seats,Free Parking',
                'is_active' => true,
            ],
            [
                'name' => 'TGV Sunway Pyramid',
                'location' => 'Sunway Pyramid Mall',
                'address' => '3, Jalan PJS 11/15',
                'city' => 'Petaling Jaya',
                'state' => 'Selangor',
                'postal_code' => '47500',
                'phone' => '03-2345 6789',
                'email' => 'sunway@tgv.com.my',
                'description' => 'Home to Malaysia\'s first IMAX with Laser and luxurious Indulge seats.',
                'facilities' => 'IMAX Laser,Indulge Seats,Beanie Cafe,Valet Parking',
                'is_active' => true,
            ],
            [
                'name' => 'MBO KSL City',
                'location' => 'KSL City Mall',
                'address' => '33, Jalan Seladang',
                'city' => 'Johor Bahru',
                'state' => 'Johor',
                'postal_code' => '80400',
                'phone' => '07-3456 7890',
                'email' => 'ksl@mbo.com.my',
                'description' => 'Largest cinema complex in Johor with 12 halls.',
                'facilities' => 'Big Screen,HALAL Snacks,Student Discount,Comfy Seats',
                'is_active' => true,
            ],
        ];

        foreach ($cinemas as $cinema) {
            Cinema::create($cinema);
        }
    
    }
}
