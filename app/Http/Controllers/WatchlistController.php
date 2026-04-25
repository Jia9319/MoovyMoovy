<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function getMoviesStatus()
    {
        $user = Auth::user();
        
        $movies = Movie::all();
        
        $userWatchlistIds = $user->watchlistMovies()->pluck('movies.id')->toArray();

        $formattedMovies = $movies->map(function ($movie) use ($userWatchlistIds) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'genre' => $movie->genre,
                'is_added' => in_array($movie->id, $userWatchlistIds),
                'poster_url' => $movie->poster_url ?: asset('storage/' . $movie->poster)
            ];
        });

        return response()->json($formattedMovies);
    }

    public function toggle($movieId)
    {
        $user = Auth::user();

        $result = $user->watchlistMovies()->toggle($movieId);

        $isAdded = count($result['attached']) > 0;

        return response()->json([
            'success' => true,
            'status' => $isAdded ? 'added' : 'removed'
        ]);
    }
}