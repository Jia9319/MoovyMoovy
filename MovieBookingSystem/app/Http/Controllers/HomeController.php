<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    // Gradient palette — assigned by movie ID so each movie looks unique
    private array $gradients = [
        'linear-gradient(145deg,#1a0033,#660094)',
        'linear-gradient(145deg,#200044,#9614d0)',
        'linear-gradient(145deg,#100020,#bb44f0)',
        'linear-gradient(145deg,#0d0020,#4a0080)',
        'linear-gradient(145deg,#1a0040,#5500aa)',
        'linear-gradient(145deg,#08000f,#9614d0)',
        'linear-gradient(145deg,#0a001a,#7700cc)',
        'linear-gradient(145deg,#180030,#440088)',
    ];

    public function index()
    {
        $featuredMovies = Movie::with('showtimes')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $nowShowing = Movie::with('showtimes')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Build a plain PHP array for the hero slider JS
        // Done here in the controller to avoid Blade parsing errors with closures inside @json()
        $moviesJson = $featuredMovies->map(function ($m) {
            $minPrice = $m->showtimes->min('price');
            return [
                'id'          => $m->id,
                'title'       => strtoupper($m->title),
                'rating'      => $m->rating ?? 'N/A',
                'duration'    => $m->duration . 'min',
                'genre'       => $m->genre,
                'year'        => $m->release_date->format('Y'),
                'description' => $m->description,
                'poster'      => $m->poster ? asset('storage/' . $m->poster) : null,
                'bgGradient'  => $this->gradients[$m->id % count($this->gradients)],
                'url'         => route('movies.show', $m->id),
                'price'       => $minPrice ? 'From RM ' . number_format($minPrice, 2) : null,
            ];
        })->values()->toArray();

        return view('home.index', compact('featuredMovies', 'nowShowing', 'moviesJson'));
    }
}