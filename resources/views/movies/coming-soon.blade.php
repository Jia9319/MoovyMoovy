@extends('layouts.app')

@section('title', 'Coming Soon - MoovyMoovy')

@section('content')

<div class="coming-soon-container">
    <div class="coming-soon-header">
        <h1>Coming Soon</h1>
        <p>Upcoming movies that you won't want to miss</p>
    </div>

    <div class="coming-soon-content">
        @if($comingSoon->isEmpty())
        <div class="empty-state">
            <i class="fas fa-calendar-alt"></i>
            <h3>No Upcoming Movies</h3>
            <p>Check back soon for exciting new releases!</p>
        </div>
        @else
        <div class="movies-grid">
            @foreach($comingSoon as $movie)
            <div class="movie-card">
                <div class="movie-poster">
                    @if($movie->poster)
                        <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}">
                    @else
                        <div class="poster-placeholder">
                            <i class="fas fa-film"></i>
                            <span>{{ $movie->title }}</span>
                        </div>
                    @endif
                    <div class="release-badge">
                        <i class="fas fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($movie->expected_release)->format('M d, Y') }}
                    </div>
                </div>
                <div class="movie-info">
                    <span class="genre">{{ $movie->genre }}</span>
                    <h3>{{ $movie->title }}</h3>
                    <p class="duration">{{ $movie->duration }} minutes</p>
                    <p class="description">{{ Str::limit($movie->description, 120) }}</p>
                    <div class="button-group">
                        <button class="btn-notify" onclick="alert('🔔 You will be notified when {{ $movie->title }} is released!')">
                            <i class="fas fa-bell"></i> Notify Me
                        </button>
                        <a href="{{ route('movies.show', $movie->id) }}" class="btn-details">
                            Details →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<style>
.coming-soon-container {
    padding: 120px 5% 60px;
    min-height: 100vh;
    background: linear-gradient(135deg, #0a0010 0%, #120020 100%);
}

.coming-soon-header {
    text-align: center;
    margin-bottom: 3rem;
}

.coming-soon-header h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 3rem;
    background: linear-gradient(135deg, #d16aff, #9614d0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.coming-soon-header p {
    color: rgba(255,255,255,0.6);
    font-size: 1.1rem;
}

.movies-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.movie-card {
    background: #120020;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.1);
    transition: transform 0.3s, border-color 0.3s;
}

.movie-card:hover {
    transform: translateY(-5px);
    border-color: #d16aff;
}

.movie-poster {
    position: relative;
    height: 380px;
    overflow: hidden;
}

.movie-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.poster-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a0033, #660094);
    gap: 1rem;
}

.poster-placeholder i {
    font-size: 3rem;
    opacity: 0.5;
}

.poster-placeholder span {
    font-size: 1.2rem;
    text-align: center;
    padding: 0 1rem;
}

.release-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(245, 158, 11, 0.9);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.movie-info {
    padding: 1.25rem;
}

.genre {
    display: inline-block;
    background: rgba(209,106,255,0.15);
    color: #d16aff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.movie-info h3 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.duration {
    color: rgba(255,255,255,0.5);
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
}

.description {
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.button-group {
    display: flex;
    gap: 0.75rem;
}

.btn-notify {
    flex: 1;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-notify:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245,158,11,0.4);
}

.btn-details {
    flex: 1;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.btn-details:hover {
    background: rgba(209,106,255,0.15);
    border-color: #d16aff;
}

.empty-state {
    text-align: center;
    padding: 4rem;
    background: #120020;
    border-radius: 20px;
    max-width: 500px;
    margin: 0 auto;
}

.empty-state i {
    font-size: 4rem;
    color: rgba(255,255,255,0.3);
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: rgba(255,255,255,0.5);
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .movies-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .movie-poster {
        height: 320px;
    }
    
    .coming-soon-header h1 {
        font-size: 2rem;
    }
}
</style>

@endsection