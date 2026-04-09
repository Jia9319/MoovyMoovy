<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks (if you have related tables)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear existing data (optional - removes all current movies)
        DB::table('movies')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $movies = [
            // Now Showing Movies
            [
                'title' => 'Inception',
                'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
                'genre' => 'Sci-Fi',
                'duration' => 148,
                'release_date' => '2010-07-16',
                'rating' => 4.8,
                'poster' => 'posters/inception.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Dark Knight',
                'description' => 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
                'genre' => 'Action',
                'duration' => 152,
                'release_date' => '2008-07-18',
                'rating' => 4.9,
                'poster' => 'posters/dark_knight.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Avatar',
                'description' => 'A paraplegic Marine dispatched to the moon Pandora on a unique mission becomes torn between following his orders and protecting the world he feels is his home.',
                'genre' => 'Sci-Fi',
                'duration' => 162,
                'release_date' => '2009-12-18',
                'rating' => 4.7,
                'poster' => 'posters/avatar.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Titanic',
                'description' => 'A seventeen-year-old aristocrat falls in love with a kind but poor artist aboard the luxurious, ill-fated R.M.S. Titanic.',
                'genre' => 'Romance',
                'duration' => 195,
                'release_date' => '1997-12-19',
                'rating' => 4.8,
                'poster' => 'posters/titanic.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'John Wick: Chapter 4',
                'description' => 'John Wick uncovers a path to defeating The High Table. But before he can earn his freedom, Wick must face off against a new enemy with powerful alliances across the globe.',
                'genre' => 'Action',
                'duration' => 169,
                'release_date' => '2023-03-24',
                'rating' => 4.6,
                'poster' => 'posters/john_wick_4.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Spider-Man: Across the Spider-Verse',
                'description' => 'Miles Morales catapults across the Multiverse, where he encounters a team of Spider-People charged with protecting its very existence.',
                'genre' => 'Animation',
                'duration' => 140,
                'release_date' => '2023-06-02',
                'rating' => 4.9,
                'poster' => 'posters/spiderverse.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // High Rated Movies
            [
                'title' => 'The Shawshank Redemption',
                'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                'genre' => 'Drama',
                'duration' => 142,
                'release_date' => '1994-09-23',
                'rating' => 4.9,
                'poster' => 'posters/shawshank.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pulp Fiction',
                'description' => 'The lives of two mob hitmen, a boxer, a gangster and his wife intertwine in four tales of violence and redemption.',
                'genre' => 'Crime',
                'duration' => 154,
                'release_date' => '1994-10-14',
                'rating' => 4.7,
                'poster' => 'posters/pulp_fiction.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Godfather',
                'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                'genre' => 'Crime',
                'duration' => 175,
                'release_date' => '1972-03-24',
                'rating' => 4.9,
                'poster' => 'posters/godfather.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Forrest Gump',
                'description' => 'The presidencies of Kennedy and Johnson, the Vietnam War, and other historical events unfold from the perspective of an Alabama man with an IQ of 75.',
                'genre' => 'Drama',
                'duration' => 142,
                'release_date' => '1994-07-06',
                'rating' => 4.8,
                'poster' => 'posters/forrest_gump.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Action Movies
            [
                'title' => 'Avengers: Endgame',
                'description' => 'After the devastating events of Avengers: Infinity War, the universe is in ruins. With the help of remaining allies, the Avengers assemble once more in order to reverse Thanos actions and restore balance to the universe.',
                'genre' => 'Action',
                'duration' => 181,
                'release_date' => '2019-04-26',
                'rating' => 4.8,
                'poster' => 'posters/endgame.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Gladiator',
                'description' => 'A former Roman General sets out to exact vengeance against the corrupt emperor who murdered his family and sent him into slavery.',
                'genre' => 'Action',
                'duration' => 155,
                'release_date' => '2000-05-05',
                'rating' => 4.7,
                'poster' => 'posters/gladiator.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Comedy Movies
            [
                'title' => 'The Hangover',
                'description' => 'Three buddies wake up from a bachelor party in Las Vegas, with no memory of the previous night and the bachelor missing. They must make their way around the city to find their friend before his wedding.',
                'genre' => 'Comedy',
                'duration' => 100,
                'release_date' => '2009-06-05',
                'rating' => 4.3,
                'poster' => 'posters/hangover.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Superbad',
                'description' => 'Two co-dependent high school seniors are forced to deal with separation anxiety after their plan to stage a booze-soaked party goes awry.',
                'genre' => 'Comedy',
                'duration' => 113,
                'release_date' => '2007-08-17',
                'rating' => 4.2,
                'poster' => 'posters/superbad.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Horror Movies
            [
                'title' => 'The Conjuring',
                'description' => 'Paranormal investigators Ed and Lorraine Warren work to help a family terrorized by a dark presence in their farmhouse.',
                'genre' => 'Horror',
                'duration' => 112,
                'release_date' => '2013-07-19',
                'rating' => 4.4,
                'poster' => 'posters/conjuring.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'A Quiet Place',
                'description' => 'In a post-apocalyptic world, a family is forced to live in silence while hiding from monsters with ultra-sensitive hearing.',
                'genre' => 'Horror',
                'duration' => 90,
                'release_date' => '2018-04-06',
                'rating' => 4.5,
                'poster' => 'posters/quiet_place.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Romance Movies
            [
                'title' => 'The Notebook',
                'description' => 'A poor yet passionate young man falls in love with a rich young woman, giving her a sense of freedom, but they are soon separated because of their social differences.',
                'genre' => 'Romance',
                'duration' => 123,
                'release_date' => '2004-06-25',
                'rating' => 4.6,
                'poster' => 'posters/notebook.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'La La Land',
                'description' => 'While navigating their careers in Los Angeles, a pianist and an actress fall in love while attempting to reconcile their aspirations for the future.',
                'genre' => 'Romance',
                'duration' => 128,
                'release_date' => '2016-12-09',
                'rating' => 4.5,
                'poster' => 'posters/lalaland.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Thriller Movies
            [
                'title' => 'Gone Girl',
                'description' => 'With his wife\'s disappearance having become the focus of an intense media circus, a man sees the spotlight turned on him when it\'s suspected that he may not be innocent.',
                'genre' => 'Thriller',
                'duration' => 149,
                'release_date' => '2014-10-03',
                'rating' => 4.7,
                'poster' => 'posters/gone_girl.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Shutter Island',
                'description' => 'In 1954, a U.S. Marshal investigates the disappearance of a murderess who escaped from a hospital for the criminally insane.',
                'genre' => 'Thriller',
                'duration' => 138,
                'release_date' => '2010-02-19',
                'rating' => 4.6,
                'poster' => 'posters/shutter_island.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert all movies
        foreach ($movies as $movie) {
            DB::table('movies')->insert($movie);
        }

        // Display success message
        $this->command->info('✅ ' . count($movies) . ' movies seeded successfully!');
        $this->command->info('Total movies in database: ' . DB::table('movies')->count());
    }
}