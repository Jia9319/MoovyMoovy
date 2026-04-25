<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;


class MovieController extends Controller
{
    public function index(Request $request)
    {
            \Log::info('Movie posters check:', Movie::all()->pluck('poster')->toArray());

        $query = Movie::nowShowing()->with('showtimes');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        if ($request->sort == 'rating') {
            $query->orderBy('rating', 'desc');
        } elseif ($request->sort == 'popular') {
            $query->orderBy('id', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if ($request->ajax() || $request->wantsJson()) {
            $movies = $query->get();
            $user = auth()->user();

            $data = $movies->map(function ($movie) use ($user) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'genre' => $movie->genre,
                    'poster_url' => $movie->poster_url,
                    'is_added' => $user ? $user->watchlist->contains($movie->id) : false,
                ];
            });
            return response()->json($data);
        }

        $movies = $query->paginate(10)->withQueryString();
        return view('movies.index', compact('movies'));
    }

    public function comingSoon()
    {
        $comingSoon = Movie::comingSoon()
            ->orderBy('expected_release', 'asc')
            ->paginate(10);

        return view('movies.coming-soon', compact('comingSoon'));
    }

    public function show(Request $request, $id)
    {
        $movie = Movie::with([
            'showtimes' => function($q) {
                $q->where('date', '>=', today())
                  ->orderBy('date')
                  ->orderBy('time');
            },
            'showtimes.cinema',
            'reviews.user',
        ])->findOrFail($id);

        if ($request->wantsJson()) {
            return response()->json([
                'movie' => $movie,
                'showtimes' => $movie->showtimes,
                'reviews' => $movie->reviews,
                'avgRating' => round($movie->reviews->avg('rating') ?: 0, 1),
                'reviewCount' => $movie->reviews->count(),
            ]);
        }

        return view('movies.show', compact('movie'));
    }

    public function create()
    {
        return view('movies.create');
    }

    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('movies.index')->with('error', 'Unauthorized');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'genre' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:now_showing,coming_soon,draft',
            'expected_release' => 'nullable|date',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }

        Movie::create($validated);
        
        return redirect()->route('movies.index')->with('success', 'Movie created successfully!');
    }

  
    public function edit($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $movie = Movie::findOrFail($id);
        return view('movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'genre' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'rating' => 'nullable|numeric|min:0|max:10',
            'poster' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:now_showing,coming_soon,draft',
            'expected_release' => 'nullable|date',
        ]);

        if ($request->hasFile('poster')) {
            if ($movie->poster)
                Storage::disk('public')->delete($movie->poster);
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }
        
        $movie->update($validated);        
        return response()->json([
            'movie' => $movie
        ]);
    }





    public function destroy($id)
    {
        // Check if user is admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect()->route('movies.index')->with('error', 'Unauthorized');
        }
        
        $movie = Movie::findOrFail($id);
        
        $movie->showtimes()->delete();
        $movie->reviews()->delete();
        if ($movie->poster)
            Storage::disk('public')->delete($movie->poster);
        $movie->delete();
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Movie deleted successfully']);
        }
        
        return redirect()->route('movies.index')->with('success', 'Movie deleted successfully');
    }
}