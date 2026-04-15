<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $nowShowing = Movie::nowShowing()
            ->with('showtimes')
            ->orderBy('release_date', 'desc')
            ->limit(5)
            ->get();
        
        $featuredMovies = Movie::nowShowing()
            ->with('showtimes')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'description' => $movie->description,
                    'poster' => $movie->poster,
                    'rating' => $movie->rating ?? '0.0',
                    'duration' => $movie->duration,
                    'genre' => $movie->genre,
                    'year' => $movie->release_date ? $movie->release_date->format('Y') : 'TBA',
                    'url' => route('booking.select', [
                        'movie_id' => $movie->id,
                        'title' => $movie->title,
                        'genre' => $movie->genre,
                        'duration' => $movie->duration
                    ]),
                ];
            });
        
        return view('home', compact('nowShowing', 'featuredMovies'));
    }
}