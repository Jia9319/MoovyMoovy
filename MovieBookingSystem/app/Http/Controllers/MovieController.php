<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    // GET /movies
    public function index(Request $request)
    {
        $query = Movie::query();

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        // Sort
        match ($request->sort) {
            'rating'   => $query->orderBy('rating', 'desc'),
            'popular'  => $query->orderBy('views', 'desc'),
            default    => $query->orderBy('created_at', 'desc'),
        };

        $movies = $query->paginate(12)->withQueryString();

        return view('movies.index', compact('movies'));
    }

    // GET /movies/create
    public function create()
    {
        return view('movies.create');
    }

    // POST /movies
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'genre'        => 'required|string|max:100',
            'duration'     => 'required|integer|min:1',
            'release_date' => 'required|date',
            'rating'       => 'nullable|numeric|min:0|max:10',
            'poster'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle poster upload
        if ($request->hasFile('poster')) {
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }

        Movie::create($validated);

        return redirect()->route('movies.index')
                         ->with('success', 'Movie added successfully.');
    }

    // GET /movies/{movie}
    public function show(Movie $movie)
    {
        // Eager load showtimes and reviews with user
        $movie->load([
            'showtimes' => fn($q) => $q->orderBy('date')->orderBy('time'),
            'reviews.user',
        ]);

        // Average rating
        $avgRating     = $movie->reviews->avg('rating');
        $reviewCount   = $movie->reviews->count();

        // Rating breakdown (1–5 stars percentage)
        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $movie->reviews->where('rating', $i)->count();
            $ratingBreakdown[$i] = $reviewCount > 0
                ? round(($count / $reviewCount) * 100)
                : 0;
        }

        return view('movies.show', compact('movie', 'avgRating', 'reviewCount', 'ratingBreakdown'));
    }

    // GET /movies/{movie}/edit
    public function edit(Movie $movie)
    {
        return view('movies.edit', compact('movie'));
    }

    // PUT /movies/{movie}
    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'genre'        => 'required|string|max:100',
            'duration'     => 'required|integer|min:1',
            'release_date' => 'required|date',
            'rating'       => 'nullable|numeric|min:0|max:10',
            'poster'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Replace poster if new one uploaded
        if ($request->hasFile('poster')) {
            // Delete old poster
            if ($movie->poster) {
                Storage::disk('public')->delete($movie->poster);
            }
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }

        $movie->update($validated);

        return redirect()->route('movies.show', $movie->id)
                         ->with('success', 'Movie updated successfully.');
    }

    // DELETE /movies/{movie}
    public function destroy(Movie $movie)
    {
        // Delete poster file
        if ($movie->poster) {
            Storage::disk('public')->delete($movie->poster);
        }

        $movie->delete();

        return redirect()->route('movies.index')
                         ->with('success', 'Movie deleted successfully.');
    }
}