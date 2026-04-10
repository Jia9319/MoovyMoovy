<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Movie;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    // GET /showtimes/create?movie_id=1
    public function create(Request $request)
    {
        // Pre-select movie if movie_id passed in query string
        $movies       = Movie::orderBy('title')->get();
        $selectedMovie = $request->movie_id
            ? Movie::findOrFail($request->movie_id)
            : null;

        return view('showtimes.create', compact('movies', 'selectedMovie'));
    }

    // POST /showtimes
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'cinema'   => 'required|string|max:255',
            'hall'     => 'nullable|string|max:100',
            'format'   => 'nullable|string|max:50',  // IMAX, 4DX, Standard
            'date'     => 'required|date|after_or_equal:today',
            'time'     => 'required',
            'price'    => 'required|numeric|min:0',
        ]);

        Showtime::create($validated);

        return redirect()->route('movies.show', $validated['movie_id'])
                         ->with('success', 'Showtime added successfully.');
    }

    // GET /showtimes/{showtime}/edit
    public function edit(Showtime $showtime)
    {
        $movies = Movie::orderBy('title')->get();

        return view('showtimes.edit', compact('showtime', 'movies'));
    }

    // PUT /showtimes/{showtime}
    public function update(Request $request, Showtime $showtime)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'cinema'   => 'required|string|max:255',
            'hall'     => 'nullable|string|max:100',
            'format'   => 'nullable|string|max:50',
            'date'     => 'required|date',
            'time'     => 'required',
            'price'    => 'required|numeric|min:0',
        ]);

        $showtime->update($validated);

        return redirect()->route('movies.show', $showtime->movie_id)
                         ->with('success', 'Showtime updated successfully.');
    }

    // DELETE /showtimes/{showtime}
    public function destroy(Showtime $showtime)
    {
        $movieId = $showtime->movie_id;
        $showtime->delete();

        return redirect()->route('movies.show', $movieId)
                         ->with('success', 'Showtime deleted successfully.');
    }
}