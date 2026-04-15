<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Showtime;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::where('is_active', true)
            ->orderBy('name')
            ->paginate(9);
        
        $cities = Cinema::where('is_active', true)
            ->distinct()
            ->pluck('city');
        
        return view('cinemas.index', compact('cinemas', 'cities'));
    }

    public function show(Cinema $cinema)
    {
        $showtimes = Showtime::whereHas('movie', function($query) {
                $query->where('status', 'now_showing');
            })
            ->where('cinema', $cinema->name)
            ->where('date', '>=', now())
            ->with('movie')
            ->orderBy('date')
            ->orderBy('time')
            ->limit(10)
            ->get();
        
        return view('cinemas.show', compact('cinema', 'showtimes'));
    }

    public function search(Request $request)
    {
        $query = Cinema::where('is_active', true);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        
        $cinemas = $query->orderBy('name')->paginate(9);
        
        if ($request->ajax()) {
            return view('cinemas.partials.grid', compact('cinemas'))->render();
        }
        
        return redirect()->route('cinemas.index');
    }
}