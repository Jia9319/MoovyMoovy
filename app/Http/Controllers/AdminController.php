<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
{
    $movies = \DB::table('movies')->get();
    
    $reportCount = \DB::table('review_reports')->count();

    return view('admin.dashboard', compact('movies', 'reportCount'));
}

    public function viewReports()
    {
        $reports = DB::table('review_reports')
            ->join('reviews', 'review_reports.review_id', '=', 'reviews.id')
            ->select('review_reports.*', 'reviews.content', 'reviews.rating')
            ->get();

        return view('admin.reports', compact('reports'));
    }
}