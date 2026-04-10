<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
{
    // Fetch movies from your database table
    $movies = \DB::table('movies')->get();
    
    // Get the count of pending reports for P4
    $reportCount = \DB::table('review_reports')->count();

    return view('admin.dashboard', compact('movies', 'reportCount'));
}

    public function viewReports()
{
    // Join reports with reviews to see what users are complaining about
    $reports = DB::table('review_reports')
        ->join('reviews', 'review_reports.review_id', '=', 'reviews.id')
        ->select('review_reports.*', 'reviews.content', 'reviews.rating')
        ->get();

    return view('admin.reports', compact('reports'));
}
}