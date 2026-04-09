<?php
 
namespace App\Http\Controllers;
 
use App\Models\Movie;
 
class HomeController extends Controller
{
    public function index()
    {
        $featuredMovies = Movie::orderBy('created_at', 'desc')->take(6)->get();
        $nowShowing     = Movie::orderBy('created_at', 'desc')->take(4)->get();
 
        return view('home.index', compact('featuredMovies', 'nowShowing'));
    }
}