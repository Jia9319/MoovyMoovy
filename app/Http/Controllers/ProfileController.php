<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $watchlistMovies = Movie::latest()->take(4)->get();

        $stats = [
            'tickets' => 12,
            'watched' => 28,
            'saved' => $watchlistMovies->count(),
            'rating' => 4.8
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

    public function history()
    {
        $user = Auth::user();

        $tickets = Ticket::query()
            ->with(['movie', 'showtime'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $stats = [
            'totalTickets' => $tickets->count(),
            'spent' => $tickets->sum('grand_total'),
        ];

        return view('profile.history', compact('user', 'tickets', 'stats'));
    }

    public function ticket(Ticket $ticket)
    {
        $user = Auth::user();

        abort_unless($ticket->user_id === $user->id, 403);

        $ticket->load(['movie', 'showtime']);

        return view('profile.details', compact('ticket'));
    }
}