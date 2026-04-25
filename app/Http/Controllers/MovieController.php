<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index(Request $request)
    {
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

    public function show(Movie $movie)
    {
        if ($movie->status === 'draft' && !(auth()->check() && auth()->user()->is_admin)) {
            abort(404);
        }

        $movie->load([
            'showtimes' => fn($q) => $q->where('date', '>=', today())->orderBy('date')->orderBy('time'),
            'reviews.user',
        ]);

        $avgRating = round($movie->reviews->avg('rating'), 1);
        $reviewCount = $movie->reviews->count();
        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $movie->reviews->where('rating', $i)->count();
            $ratingBreakdown[$i] = $reviewCount > 0 ? round(($count / $reviewCount) * 100) : 0;
        }

        return view('movies.show', compact('movie', 'avgRating', 'reviewCount', 'ratingBreakdown'));
    }

    public function create()
    {
        return view('movies.create');
    }

    public function store(Request $request)
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
            $validated['poster'] = $request->file('poster')->store('posters', 'public');
        }

        Movie::create($validated);
        return redirect()->route('movies.index')->with('success', 'Movie added successfully!');
    }

    public function edit(Movie $movie)
    {
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
        return redirect()->route('movies.show', $movie->id)->with('success', 'Movie updated!');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->poster)
            Storage::disk('public')->delete($movie->poster);
        $movie->delete();
        return redirect()->route('movies.index')->with('success', 'Movie deleted!');
    }
}