<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function movies()
    {
        $movies = \App\Models\Movie::all();
        return view('admin.movies', compact('movies'));
    }

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

    public function viewReports()
{
    $reports = DB::table('review_reports')
        ->leftJoin('reviews', 'review_reports.review_id', '=', 'reviews.id')
        ->leftJoin('users', 'review_reports.user_id', '=', 'users.id')
        ->select(
            'review_reports.*',
            'reviews.content',
            'reviews.rating',
            'users.name as reporter_name'
        )
        ->get();

    return view('admin.reports', compact('reports'));
}
}