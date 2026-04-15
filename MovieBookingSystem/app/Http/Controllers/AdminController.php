<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $movies = DB::table('movies')->get();
        $reportCount = DB::table('review_reports')->count();
        return view('admin.dashboard', compact('movies', 'reportCount'));
    }

    public function movies()
    {
        $movies = Movie::all();
        return view('admin.movies', compact('movies'));
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'showtime.movie'])->get();
        return view('admin.bookings', compact('bookings'));
    }

    public function reviews()
    {
        $reviews = \App\Models\Review::with('user', 'movie')->get();
        return view('admin.reviews', compact('reviews'));
    }

    public function deleteReview($id)
    {
        \App\Models\Review::findOrFail($id)->delete();
        return redirect()->route('admin.reviews')->with('success', 'Review deleted successfully!');
    }

    public function viewReports()
    {
        $totalMovies   = Movie::count();
        $totalBookings = Booking::count();
        $totalUsers    = User::count();
        $totalRevenue  = Booking::sum('total_price');
        $topMovies     = collect();

        return view('admin.reports', compact(
            'totalMovies', 'totalBookings', 'totalUsers', 'totalRevenue', 'topMovies'
        ));
    }
}