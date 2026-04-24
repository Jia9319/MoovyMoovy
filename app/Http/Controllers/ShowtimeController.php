<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cinema;

class ShowtimeController extends Controller
{
    public function create(Request $request)
    {
        $movies = Movie::orderBy('title')->get();
        $selectedMovie = $request->movie_id
             ? Movie::findOrFail($request->movie_id)
             : null;
        return view('showtimes.create', compact('movies', 'selectedMovie'));
    }    
        
    public function store(Request $request, $movieId)
    {
        // Debug log
        \Log::info('Showtime store called', [
            'movieId' => $movieId,
            'data' => $request->all(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role ?? null
        ]);

        if (!Auth::check()) {
            return response()->json(['message' => 'Please login first'], 401);
        }

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Admin access required'], 403);
        }

        // Find movie
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cinema_id' => 'required|exists:cinemas,id',  // Uses cinema_id
            'hall'   => 'nullable|string|max:100',
            'format' => 'nullable|string|max:50',
            'date'   => 'required|date',
            'time'   => 'required',
            'price'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $cinema = Cinema::find($request->cinema_id);
        $showtime = Showtime::create([
            'movie_id' => $movie->id,
            'cinema_id' => $request->cinema_id,
            'cinema_name' => $cinema->name,
            'hall'   => $request->hall,
            'format' => $request->format,
            'date'   => $request->date,
            'time'   => $request->time,
            'price'  => $request->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Showtime added successfully!',
            'showtime' => $showtime
        ], 201);
    
    }

    public function destroy($id)
    {
        // Check if user is logged in via session
        if (!Auth::check()) {
            return response()->json(['message' => 'Please login first'], 401);
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Check if user has admin role
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Admin access required'], 403);
        }

        $showtime = Showtime::find($id);
        if (!$showtime) {
            return response()->json(['message' => 'Showtime not found'], 404);
        }

        $showtime->delete();

        return response()->json([
            'success' => true,
            'message' => 'Showtime deleted successfully!'
        ]);
    }

public function update(Request $request, $id)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Please login first'], 401);
    }

    $user = Auth::user();
    
    if ($user->role !== 'admin') {
        return response()->json(['message' => 'Admin access required'], 403);
    }

    $showtime = Showtime::find($id);
    if (!$showtime) {
        return response()->json(['message' => 'Showtime not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'cinema_id' => 'required|exists:cinemas,id',
        'hall'   => 'nullable|string|max:100',
        'format' => 'nullable|string|max:50',
        'date'   => 'required|date',
        'time'   => 'required',
        'price'  => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    // Get cinema name
    $cinema = Cinema::find($request->cinema_id);
    
    $showtime->update([
        'cinema_id' => $request->cinema_id,
        'cinema_name' => $cinema->name,
        'hall'   => $request->hall,
        'format' => $request->format,
        'date'   => $request->date,
        'time'   => $request->time,
        'price'  => $request->price,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Showtime updated successfully!'
    ]);
}
}