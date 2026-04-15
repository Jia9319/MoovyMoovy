@extends('layouts.app')

@section('content')
<style>
    html {
        scroll-behavior: smooth;
    }

    #app,
    .py-4,
    .container,
    .container-fluid {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    :root {
        --moovy-purple: #d16aff;
        --moovy-bg: #0a0510;
        --sidebar-bg: #11091a;
        --card-bg: #160d21;
        --text-gray: #8c839d;
        --header-height: 70px;
    }

    body {
        background-color: var(--moovy-bg) !important;
        color: white;
    }

    .dashboard-wrapper {
        display: flex;
        width: 100%;
        min-height: 100vh;
        padding-top: var(--header-height);
    }

    .sidebar-nav {
        width: 240px;
        background: var(--sidebar-bg);
        border-right: 1px solid #231633;
        padding: 20px 15px;
        position: sticky;
        top: var(--header-height);
        height: calc(100vh - var(--header-height));
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start !important; 
    }

    .profile-section {
        width: 100%;
        text-align: center;
        margin-bottom: 10px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .avatar-circle {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        border: 3px solid var(--moovy-purple);
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: var(--moovy-purple);
        background: #1a1026;
    }

    .menu-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 10px;
    }

    .menu-link {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 18px;
        color: var(--text-gray);
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .menu-link.active {
        background: var(--moovy-purple) !important;
        color: white !important;
        font-weight: 500;
    }

    .menu-link:hover:not(.active) {
        background: rgba(209, 106, 255, 0.1);
        color: white;
    }

    .main-canvas {
        flex-grow: 1;
        padding: 40px 60px;
    }

    .stats-layout {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: var(--card-bg);
        padding: 25px;
        border-radius: 20px;
        text-align: center;
        border: 1px solid #2d1b40;
    }

    .stat-card h2 {
        color: var(--moovy-purple);
        font-size: 32px;
        margin: 0;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 40px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 20px;
        font-weight: bold;
    }

    .view-link {
        color: var(--moovy-purple);
        font-size: 13px;
        text-decoration: none;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .activity-item {
        background: var(--card-bg);
        padding: 15px 20px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        border: 1px solid #231633;
    }

    .activity-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: #221a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 20px;
    }

    .payment-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .payment-card {
        background: var(--card-bg);
        padding: 18px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #231633;
    }

    .card-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .watchlist-container {
        margin-top: 50px;
    }

    .watchlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
    }

    .movie-poster-wrapper {
        cursor: pointer;
        transition: transform 0.3s;
    }

    .movie-poster-wrapper:hover {
        transform: translateY(-5px);
    }

    .poster-image {
        width: 100%;
        aspect-ratio: 2 / 3;
        object-fit: cover;
        border-radius: 20px;
        background: #1a1026;
        border: 1px solid #2d1b40;
    }

    .movie-info {
        margin-top: 12px;
    }

    .movie-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .movie-meta {
        font-size: 12px;
        color: var(--text-gray);
        margin-top: 4px;
    }

    .add-more-poster {
        aspect-ratio: 2 / 3;
        border: 2px dashed #3d2b52;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--moovy-purple);
        background: transparent;
    }

    #recent-activity,
    #payment-methods,
    #watchlist-section {
        scroll-margin-top: 100px;
    }
</style>

<div class="dashboard-wrapper">
    <nav class="sidebar-nav">
        <div class="profile-section">
            <div class="avatar-circle">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <h4 style="margin-bottom: 2px; font-size: 18px;">{{ Auth::user()->name }}</h4>
            <p style="color: var(--moovy-purple); font-size: 10px; margin-bottom: 0;">PLATINUM MEMBER</p>
        </div>

        <div class="menu-container">
            <a href="#" class="menu-link active">👤 Profile</a>
            <a href="#recent-activity" class="menu-link">🕒 Recent Activity</a>
            <a href="#payment-methods" class="menu-link">💳 Payment Methods</a>
            <a href="#watchlist-section" class="menu-link">⭐ Watchlist</a>
        </div>
    </nav>

    <main class="main-canvas">
        <div class="stats-layout">
            <div class="stat-card">
                <h2>12</h2>
                <p style="color: var(--text-gray); font-size: 11px;">TICKETS</p>
            </div>
            <div class="stat-card">
                <h2>28</h2>
                <p style="color: var(--text-gray); font-size: 11px;">WATCHED</p>
            </div>
            <div class="stat-card">
                <h2>{{ $watchlistMovies->count() }}</h2>
                <p style="color: var(--text-gray); font-size: 11px;">SAVED</p>
            </div>
            <div class="stat-card">
                <h2>4.8</h2>
                <p style="color: var(--text-gray); font-size: 11px;">AVG RATING</p>
            </div>
        </div>

        <div class="content-grid">
            <div id="recent-activity">
                <div class="section-header">
                    <span class="section-title">Recent Activity</span>
                    <a href="#" class="view-link">View All</a>
                </div>
                <div class="activity-list">
                    @forelse($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-icon">{{ $activity->type == 'food' ? '🍿' : '🎬' }}</div>
                            <div style="flex: 1;">
                                <div style="font-size: 15px; font-weight: 500;">{{ $activity->description }}</div>
                                <div style="font-size: 12px; color: var(--text-gray);">
                                    {{ $activity->created_at->format('M d') }} • {{ $activity->location }}</div>
                            </div>
                            <div style="font-weight: bold; font-size: 16px;">${{ number_format($activity->amount, 2) }}</div>
                        </div>
                    @empty
                        <p style="color: var(--text-gray); font-size: 13px;">No recent activities found.</p>
                    @endforelse
                </div>
            </div>

            <div id="payment-methods">
                <div class="section-header">
                    <span class="section-title">Payment Methods</span>
                </div>
                <div class="payment-list">
                    @forelse($paymentMethods as $method)
                        <div class="payment-card">
                            <div class="card-info">
                                <div style="font-size: 24px;">{{ $method->card_type == 'visa' ? '💳' : '📱' }}</div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 500;">{{ $method->provider_name }}</div>
                                    <div style="font-size: 12px; color: var(--text-gray);">**** **** **** {{ $method->last_four }}</div>
                                </div>
                            </div>
                            @if($method->is_default)
                                <span style="font-size: 10px; color: var(--moovy-purple); font-weight: bold; border: 1px solid var(--moovy-purple); padding: 2px 8px; border-radius: 6px;">DEFAULT</span>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--text-gray); font-size: 13px;">No payment methods linked.</p>
                    @endforelse
                    <button style="width: 100%; padding: 14px; border-radius: 15px; border: 1px dashed #3d2b52; background: transparent; color: var(--moovy-purple); font-weight: 600; cursor: pointer; margin-top: 5px;">+ Add New Method</button>
                </div>
            </div>
        </div>

        <div id="watchlist-section" class="watchlist-container">
            <div class="section-header">
                <span class="section-title">Watchlist</span>
                <a href="#" class="view-link">Edit List</a>
            </div>
            <div class="watchlist-grid">
                @foreach($watchlistMovies as $movie)
                    <div class="movie-poster-wrapper">
                        <img src="{{ $movie->poster }}" class="poster-image" alt="{{ $movie->title }}">
                        <div class="movie-info">
                            <p class="movie-title">{{ $movie->title }}</p>
                            <p class="movie-meta">{{ $movie->genre }} • {{ $movie->year }}</p>
                        </div>
                    </div>
                @endforeach
                <div class="movie-poster-wrapper">
                    <div class="add-more-poster">
                        <div style="font-size: 30px; font-weight: bold;">+</div>
                        <div style="font-size: 12px; margin-top: 8px; font-weight: bold;">Add More</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.menu-link');
        links.forEach(link => {
            link.addEventListener('click', function() {
                links.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
@endsection