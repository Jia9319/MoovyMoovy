<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $watchedCount = \App\Models\Ticket::where('user_id', $user->id)->count();
        $watchlistMovies = $user->watchlistMovies()->latest()->get();
        $averageRating = $user->reviews()->avg('rating');

        $stats = [
            'watched' => $watchedCount,
            'saved' => $watchlistMovies->count(),
            'rating' => $averageRating ? number_format($averageRating, 1) : '0.0'
        ];
        
       $recentTickets = \App\Models\Ticket::where('user_id', $user->id)
        ->with('movie') 
        ->latest()
        ->get()
        ->unique('movie_id') 
        ->take(5); 



        return view('profile.profile', compact('user', 'watchlistMovies', 'stats','recentTickets'));
    }
}