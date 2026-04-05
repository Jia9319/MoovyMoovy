<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $exists = Review::where('movie_id', $movie->id)
                        ->where('user_id', Auth::id())
                        ->exists();

        if ($exists) {
            return back()->with('error', 'You have already reviewed this movie.');
        }

        Review::create([
            'movie_id'     => $movie->id,
            'user_id'      => Auth::id(),
            'rating'       => $validated['rating'],
            'title'        => $validated['title'] ?? null,
            'content'      => $validated['content'],
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    public function update(Request $request, Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $review->update([
            'rating'       => $validated['rating'],
            'title'        => $validated['title'] ?? null,
            'content'      => $validated['content'],
            'is_anonymous' => $request->boolean('is_anonymous'),
        ]);

        return back()->with('success', 'Review updated!');
    }

    public function destroy(Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403);
        $review->delete();
        return back()->with('success', 'Review deleted!');
    }

    public function like(Review $review)
    {
        $userId       = Auth::id();
        $alreadyLiked = $review->likes()->where('user_id', $userId)->exists();

        if ($alreadyLiked) {
            $review->likes()->where('user_id', $userId)->delete();
            $liked = false;
        } else {
            $review->likes()->create(['user_id' => $userId]);
            $liked = true;
        }

        return response()->json([
            'liked'      => $liked,
            'like_count' => $review->likes()->count(),
        ]);
    }

    public function report(Request $request, Review $review)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        $alreadyReported = $review->reports()->where('user_id', Auth::id())->exists();
        if ($alreadyReported) {
            return response()->json(['message' => 'Already reported.'], 422);
        }

        $review->reports()->create([
            'user_id' => Auth::id(),
            'reason'  => $request->reason,
        ]);

        return response()->json(['message' => 'Review reported. Thank you.']);
    }
}