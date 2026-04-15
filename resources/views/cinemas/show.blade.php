@extends('layouts.app')

@section('title', $cinema->name . ' - MoovyMoovy')

@section('content')

<div class="cinema-detail-page">
    <div class="cinema-banner">
        @if($cinema->image)
            <div class="banner-image" style="background-image: url('{{ $cinema->image_url }}');"></div>
        @else
            <div class="banner-placeholder">
                <i class="fas fa-building"></i>
            </div>
        @endif
        <div class="banner-overlay"></div>
        <div class="banner-content">
            <h1>{{ $cinema->name }}</h1>
            <div class="cinema-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $cinema->location }}, {{ $cinema->city }}</span>
            </div>
        </div>
    </div>

    <div class="cinema-detail-container">
        <div class="cinema-info-section">
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> About</h3>
                <p>{{ $cinema->description ?? 'Experience the best movie viewing experience at ' . $cinema->name . '. Our state-of-the-art facilities and comfortable seating ensure an unforgettable cinematic journey.' }}</p>
            </div>

            <div class="info-card">
                <h3><i class="fas fa-map-pin"></i> Location</h3>
                <p><strong>Address:</strong> {{ $cinema->address }}, {{ $cinema->city }}, {{ $cinema->state }} {{ $cinema->postal_code }}</p>
                <p><strong>Phone:</strong> {{ $cinema->phone ?? 'Not available' }}</p>
                <p><strong>Email:</strong> {{ $cinema->email ?? 'Not available' }}</p>
            </div>

            <div class="info-card">
                <h3><i class="fas fa-cogs"></i> Facilities</h3>
                <div class="facilities-list">
                    @foreach($cinema->facilities_array as $facility)
                        <span class="facility-tag">
                            <i class="fas fa-check-circle"></i> {{ $facility }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="showtimes-section">
            <h3><i class="fas fa-clock"></i> Now Showing at {{ $cinema->name }}</h3>
            @if($showtimes->isEmpty())
            <div class="no-showtimes">
                <i class="fas fa-calendar-times"></i>
                <p>No showtimes available at this cinema.</p>
            </div>
            @else
            <div class="showtimes-list">
                @foreach($showtimes as $showtime)
                <div class="showtime-item">
                    <div class="movie-info">
                        <div class="movie-poster-small">
                            @if($showtime->movie->poster)
                                <img src="{{ asset('storage/' . $showtime->movie->poster) }}" alt="{{ $showtime->movie->title }}">
                            @else
                                <i class="fas fa-film"></i>
                            @endif
                        </div>
                        <div class="movie-details">
                            <h4>{{ $showtime->movie->title }}</h4>
                            <div class="movie-meta">
                                <span><i class="fas fa-star"></i> {{ $showtime->movie->rating ?? '0.0' }}</span>
                                <span><i class="fas fa-clock"></i> {{ $showtime->movie->duration }} min</span>
                                <span><i class="fas fa-tag"></i> {{ $showtime->movie->genre }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="showtime-details">
                        <div class="showtime-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($showtime->date)->format('l, d M Y') }}
                        </div>
                        <div class="showtime-time">
                            <i class="far fa-clock"></i>
                            {{ \Carbon\Carbon::parse($showtime->time)->format('h:i A') }}
                        </div>
                        <div class="showtime-price">
                            RM {{ number_format($showtime->price, 2) }}
                        </div>
                        <a href="{{ route('booking.seat', ['movie_id' => $showtime->movie_id, 'showtime_id' => $showtime->id]) }}" class="btn-book-now">
                            Book Now →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.cinema-detail-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0010 0%, #120020 100%);
}

.cinema-banner {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.banner-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
}

.banner-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1a0033, #660094);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 5rem;
    color: rgba(255,255,255,0.3);
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(10,0,16,0.9));
}

.banner-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 5%;
}

.banner-content h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 3.5rem;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}

.cinema-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #d16aff;
    font-size: 1rem;
}

.cinema-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem 5%;
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}

.info-card {
    background: #120020;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.info-card h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #d16aff;
}

.info-card p {
    color: rgba(255,255,255,0.7);
    line-height: 1.6;
    margin-bottom: 0.5rem;
}

.facilities-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.facility-tag {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(209,106,255,0.15);
    color: #d16aff;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
}

.showtimes-section {
    background: #120020;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid rgba(255,255,255,0.1);
}

.showtimes-section h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #d16aff;
}

.showtimes-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.showtime-item {
    background: rgba(0,0,0,0.3);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    transition: all 0.3s;
}

.showtime-item:hover {
    background: rgba(209,106,255,0.1);
}

.movie-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 2;
}

.movie-poster-small {
    width: 50px;
    height: 70px;
    background: linear-gradient(135deg, #1a0033, #660094);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.movie-poster-small img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.movie-poster-small i {
    font-size: 1.5rem;
    color: rgba(255,255,255,0.3);
}

.movie-details h4 {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.movie-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.7rem;
    color: rgba(255,255,255,0.5);
}

.movie-meta i {
    margin-right: 0.25rem;
}

.showtime-details {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.showtime-date, .showtime-time {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.7);
}

.showtime-date i, .showtime-time i {
    margin-right: 0.25rem;
    color: #d16aff;
}

.showtime-price {
    font-size: 1rem;
    font-weight: 700;
    color: #ffc107;
}

.btn-book-now {
    background: linear-gradient(135deg, #9614d0, #660094);
    color: white;
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-book-now:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(150,20,208,0.4);
}

.no-showtimes {
    text-align: center;
    padding: 3rem;
    color: rgba(255,255,255,0.5);
}

.no-showtimes i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .cinema-detail-container {
        grid-template-columns: 1fr;
    }
    
    .banner-content h1 {
        font-size: 2rem;
    }
    
    .showtime-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .showtime-details {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

@endsection