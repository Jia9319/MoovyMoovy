@extends('layouts.app')

@section('title', $movie->title . ' - MoovyMoovy')

@section('content')
<!-- Movie Hero Section -->
<div class="movie-hero" style="position: relative; min-height: 70vh; background: linear-gradient(135deg, #1a0033, #0a0018); overflow: hidden;">
    <div class="hero-content" style="position: relative; z-index: 2; padding: 120px 5% 4rem;">
        <div class="movie-details" style="max-width: 600px;">
            <h1 class="movie-title" style="font-family: 'Bebas Neue'; font-size: 4rem; margin-bottom: 1rem;">
                {{ $movie->title }}
            </h1>
            <div class="movie-meta" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; color: var(--muted);">
                <span>★ {{ $movie->rating ?? 'N/A' }}</span>
                <span>•</span>
                <span>{{ $movie->duration }} min</span>
                <span>•</span>
                <span>{{ $movie->genre }}</span>
                <span>•</span>
                <span>{{ date('Y', strtotime($movie->release_date)) }}</span>
            </div>
            <p class="movie-description" style="color: var(--muted); line-height: 1.6; margin-bottom: 2rem;">
                {{ $movie->description }}
            </p>
            <div class="movie-actions" style="display: flex; gap: 1rem;">
                <a href="{{ route('booking.select', ['movie_id' => $movie->id, 'title' => $movie->title, 'genre' => $movie->genre, 'duration' => $movie->duration, 'poster' => $movie->poster]) }}" class="book-ticket-btn">
                    Book Tickets
                </a>
                @auth
                <button class="btn-edit" onclick="location.href='{{ route('movies.edit', $movie->id) }}'" 
                    style="background: rgba(255,255,255,0.1); color: white; border: 1px solid var(--border); padding: 0.875rem 2rem; border-radius: 8px; cursor: pointer;">
                    Edit Movie
                </button>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Showtimes Section -->
<section style="padding: 4rem 5%; background: var(--bg);">
    <div class="sec-head" style="margin-bottom: 2rem;">
        <h2 class="sec-title">Showtimes</h2>
        @auth
        <a href="{{ route('showtimes.create', ['movie_id' => $movie->id]) }}" class="btn-add" 
           style="background: var(--grad-2); color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none;">
            + Add Showtime
        </a>
        @endauth
    </div>
    
    <div class="showtimes-grid" style="display: grid; gap: 1rem;">
        @forelse($movie->showtimes as $showtime)
        <div class="showtime-card" style="background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 600;">{{ $showtime->cinema }}</div>
                <div style="color: var(--muted); font-size: 0.875rem;">
                    {{ date('l, d M Y', strtotime($showtime->date)) }} at {{ date('h:i A', strtotime($showtime->time)) }}
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div style="color: var(--c1); font-weight: 600;">RM {{ $showtime->price }}</div>
                <a href="{{ route('booking.seat', ['movie_id' => $movie->id, 'title' => $movie->title, 'genre' => $movie->genre, 'duration' => $movie->duration, 'poster' => $movie->poster, 'cinema' => $showtime->cinema, 'hall' => $showtime->hall, 'format' => $showtime->format, 'date' => $showtime->date, 'time' => $showtime->time, 'price' => $showtime->price]) }}" style="background: var(--grad-2); color: white; border: none; border-radius: 8px; padding: 0.5rem 0.9rem; text-decoration: none; font-size: 0.82rem; font-weight: 600;">
                    Buy Ticket
                </a>
                @auth
                <button onclick="location.href='{{ route('showtimes.edit', $showtime->id) }}'" class="edit-showtime" 
                    style="background: none; border: none; color: var(--c1); cursor: pointer;">
                    <i class="fas fa-edit"></i>
                </button>
                <form action="{{ route('showtimes.destroy', $showtime->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-showtime" onclick="return confirm('Delete this showtime?')"
                        style="background: none; border: none; color: #ff4444; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endauth
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 2rem; color: var(--muted);">
            No showtimes available for this movie yet.
        </div>
        @endforelse
    </div>
</section>

<!-- Reviews Section -->
<section style="padding: 2rem 5% 4rem; background: var(--bg);">
    <div class="sec-head" style="margin-bottom: 2rem;">
        <h2 class="sec-title">Reviews</h2>
        <button class="btn-write-review" id="openReviewModal" 
            style="background: var(--grad-2); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer;">
            <i class="fas fa-pen"></i> Write a Review
        </button>
    </div>
    
    <!-- Reviews List -->
    <div class="reviews-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @forelse($movie->reviews as $review)
        <div class="review-card" style="background: var(--card); border-radius: 16px; padding: 1.5rem; border: 1px solid var(--border);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="avatar" style="width: 48px; height: 48px; background: var(--grad-2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 600;">{{ $review->is_anonymous ? 'Anonymous' : $review->user->name }}</h4>
                        <div style="color: #ffc107; font-size: 0.875rem;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                <div style="color: var(--muted); font-size: 0.75rem;">
                    {{ $review->created_at->diffForHumans() }}
                </div>
            </div>
            <h5 style="font-size: 1rem; margin-bottom: 0.5rem;">{{ $review->title }}</h5>
            <p style="color: var(--muted); line-height: 1.6; margin-bottom: 1rem;">{{ $review->content }}</p>
            
            @auth
            @if(auth()->id() == $review->user_id)
            <div style="display: flex; gap: 1rem;">
                <button class="edit-review" data-review-id="{{ $review->id }}" 
                    style="background: none; border: none; color: var(--c1); cursor: pointer;">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-review" onclick="return confirm('Delete this review?')"
                        style="background: none; border: none; color: #ff4444; cursor: pointer;">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
            @endif
            @endauth
        </div>
        @empty
        <div style="text-align: center; padding: 2rem; color: var(--muted);">
            No reviews yet. Be the first to review!
        </div>
        @endforelse
    </div>
</section>


<style>
.btn-write-review:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(150,20,208,0.4);
}

.review-card:hover {
    border-color: var(--c1);
    transform: translateX(5px);
}
</style>
@endsection