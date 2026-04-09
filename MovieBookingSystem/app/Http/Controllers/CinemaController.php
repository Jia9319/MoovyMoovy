<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CinemaController extends Controller
{
    // Static cinema data — replace with DB when you have a cinemas table
    private array $cinemas = [
        ['id' => 1,  'name' => 'MoovyMoovy Pavilion KL',    'state' => 'Kuala Lumpur', 'loc' => 'Bukit Bintang, Kuala Lumpur', 'tags' => ['IMAX', 'Dolby', 'VIP']],
        ['id' => 2,  'name' => 'MoovyMoovy Sunway Pyramid', 'state' => 'Selangor',     'loc' => 'Petaling Jaya, Selangor',    'tags' => ['IMAX', '4DX', 'ScreenX']],
        ['id' => 3,  'name' => 'MoovyMoovy IOI City Mall',  'state' => 'Selangor',     'loc' => 'Putrajaya',                  'tags' => ['Dolby', 'Beanieplex']],
        ['id' => 4,  'name' => 'MoovyMoovy Mid Valley',     'state' => 'Kuala Lumpur', 'loc' => 'Kuala Lumpur',              'tags' => ['IMAX', 'Premier']],
        ['id' => 5,  'name' => 'MoovyMoovy KLCC',           'state' => 'Kuala Lumpur', 'loc' => 'Kuala Lumpur',              'tags' => ['4DX', 'VIP', 'Dolby']],
        ['id' => 6,  'name' => 'MoovyMoovy 1 Utama',        'state' => 'Selangor',     'loc' => 'Petaling Jaya, Selangor',    'tags' => ['IMAX', 'ScreenX']],
        ['id' => 7,  'name' => 'MoovyMoovy Gurney Plaza',   'state' => 'Penang',       'loc' => 'George Town, Penang',        'tags' => ['IMAX', 'Dolby']],
        ['id' => 8,  'name' => 'MoovyMoovy Queensbay',      'state' => 'Penang',       'loc' => 'Bayan Lepas, Penang',        'tags' => ['4DX', 'VIP']],
        ['id' => 9,  'name' => 'MoovyMoovy Setia City',     'state' => 'Selangor',     'loc' => 'Shah Alam, Selangor',        'tags' => ['Dolby', 'Beanieplex']],
        ['id' => 10, 'name' => 'MoovyMoovy Aeon Tebrau',    'state' => 'Johor',        'loc' => 'Johor Bahru, Johor',         'tags' => ['IMAX', 'ScreenX']],
        ['id' => 11, 'name' => 'MoovyMoovy Ipoh Parade',    'state' => 'Perak',        'loc' => 'Ipoh, Perak',                'tags' => ['Dolby', 'VIP']],
        ['id' => 12, 'name' => 'MoovyMoovy Melaka',         'state' => 'Melaka',       'loc' => 'Melaka City',                'tags' => ['IMAX', '4DX']],
    ];

    public function index(Request $request)
    {
        $cinemas   = collect($this->cinemas);
        $locations = $cinemas->pluck('state')->unique()->sort()->values();

        if ($request->filled('location')) {
            $cinemas = $cinemas->where('state', $request->location);
        }

        return view('cinemas.index', compact('cinemas', 'locations'));
    }

    public function show($id)
    {
        $cinema = collect($this->cinemas)->firstWhere('id', (int) $id);
        if (!$cinema) abort(404);

        // Get showtimes at this cinema from DB
        $showtimes = \App\Models\Showtime::with('movie')
            ->where('cinema', $cinema['name'])
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('movie_id');

        return view('cinemas.show', compact('cinema', 'showtimes'));
    }
}