<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $watchlistMovies = $user->watchlistMovies()->latest()->get();

        $averageRating = $user->reviews()->avg('rating');

        $stats = [
            'tickets' => 12,
            'watched' => 28,
            'saved' => $watchlistMovies->count(),
            'rating' => $averageRating ? number_format($averageRating, 1) : '0.0'
        ];
        
        $recentActivities = collect([]);
        $paymentMethods = collect([]);

        $activeTickets = [
            (object) [
                'booking_id' => 'CP-99281-RT',
                'show_time' => 'Tonight • 8:30 PM',
                'location' => 'Grand Cinema • Hall 4',
                'movie' => Movie::first() ?? (object) ['title' => 'No Movie', 'poster' => '']
            ]
        ];

        return view('profile.profile', compact('user', 'watchlistMovies', 'stats', 'activeTickets','recentActivities', 
        'paymentMethods'));
    }
}