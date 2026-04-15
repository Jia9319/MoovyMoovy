<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Showtime;
use Carbon\Carbon;
class ShowtimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $showtimes = [
            // ===== GSC Mid Valley (Cinema ID: 1) =====
            // Gayong 2
            [
                'movie_id' => 1, // Gayong 2
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'Hall 1',
                'format' => '2D',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '19:00:00',
                'price' => 18.00,
                
            ],
            [
                'movie_id' => 1,
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'Hall 2',
                'format' => 'IMAX',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '22:00:00',
                'price' => 28.00,
            ],
            
            // The Super Mario Galaxy Movie
            [
                'movie_id' => 2, // The Super Mario Galaxy Movie
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'Hall 3',
                'format' => '3D',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '16:00:00',
                'price' => 22.00,
            ],
            [
                'movie_id' => 2,
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'Hall 1',
                'format' => '2D',
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time' => '13:00:00',
                'price' => 18.00,
            ],
            
            // Project Hail Mary
            [
                'movie_id' => 4, // Project Hail Mary
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'IMAX Hall',
                'format' => 'IMAX',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '20:00:00',
                'price' => 32.00,
            ],
            
            // Toy Story 5
            [
                'movie_id' => 19, // Toy Story 5
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'Hall 4',
                'format' => '3D',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '14:00:00',
                'price' => 22.00,
            ],
            [
                'movie_id' => 19,
                'cinema_id' => 1,
                'cinema' => 'GSC Mid Valley',
                'hall' => 'Hall 5',
                'format' => '2D',
                'date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                'time' => '11:00:00',
                'price' => 16.00,
            ],

            // Gayong 2
            [
                'movie_id' => 1,
                'cinema_id' => 2,
                'cinema' => 'TGV Sunway Pyramid',
                'hall' => 'IMAX Hall',
                'format' => 'IMAX',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '21:00:00',
                'price' => 30.00,
            ],
            [
                'movie_id' => 1,
                'cinema_id' => 2,
                'cinema' => 'TGV Sunway Pyramid',
                'hall' => 'Hall 2',
                'format' => '2D',
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time' => '18:00:00',
                'price' => 18.00,
            ],
            
            // The Super Mario Galaxy Movie
            [
                'movie_id' => 2,
                'cinema_id' => 2,
                'cinema' => 'TGV Sunway Pyramid',
                'hall' => 'Indulge Hall',
                'format' => '2D',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '15:00:00',
                'price' => 25.00,
            ],
            
            // Project Hail Mary
            [
                'movie_id' => 4,
                'cinema_id' => 2,
                'cinema' => 'TGV Sunway Pyramid',
                'hall' => 'IMAX Hall',
                'format' => 'IMAX',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '19:30:00',
                'price' => 32.00,
            ],
            
            // Toy Story 5
            [
                'movie_id' => 19,
                'cinema_id' => 2,
                'cinema' => 'TGV Sunway Pyramid',
                'hall' => 'Hall 5',
                'format' => '3D',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '14:30:00',
                'price' => 24.00,
            ],

            // Gayong 2
            [
                'movie_id' => 1,
                'cinema_id' => 3,
                'cinema' => 'MBO KSL City',
                'hall' => 'Hall 8',
                'format' => '2D',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '20:00:00',
                'price' => 16.00,
            ],
            
            // The Super Mario Galaxy Movie
            [
                'movie_id' => 2,
                'cinema_id' => 3,
                'cinema' => 'MBO KSL City',
                'hall' => 'Hall 6',
                'format' => '2D',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '15:30:00',
                'price' => 16.00,
            ],
            
            // LIK: Love Insurance Kompany
            [
                'movie_id' => 3, // LIK
                'cinema_id' => 3,
                'cinema' => 'MBO KSL City',
                'hall' => 'Hall 7',
                'format' => '2D',
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time' => '18:00:00',
                'price' => 15.00,
            ],
            
            // Project Hail Mary
            [
                'movie_id' => 4,
                'cinema_id' => 3,
                'cinema' => 'MBO KSL City',
                'hall' => 'Big Screen Hall',
                'format' => '2D',
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time' => '21:00:00',
                'price' => 18.00,
            ],
            
            // Toy Story 5
            [
                'movie_id' => 19,
                'cinema_id' => 3,
                'cinema' => 'MBO KSL City',
                'hall' => 'Hall 9',
                'format' => '2D',
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time' => '13:00:00',
                'price' => 15.00,
            ],
        ];

        foreach ($showtimes as $showtime) {
            $showtime['created_at'] = now();
            $showtime['updated_at'] = now();
            Showtime::create($showtime);
        }

        $this->command->info('Showtimes seeded successfully!');
        $this->command->info('Total showtimes created: ' . count($showtimes));
    
    }
}
