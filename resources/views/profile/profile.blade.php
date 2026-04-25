@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
        font-family: 'Inter', sans-serif !important;
        -webkit-font-smoothing: antialiased;
    }

    .modal-scroll-area::-webkit-scrollbar {
        width: 6px;
    }

    .modal-scroll-area::-webkit-scrollbar-thumb {
        background: #3d2b52;
        border-radius: 10px;
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
        padding: 12px 18px;
        color: var(--text-gray);
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 14px;
    }

    .menu-link.active {
        background: var(--moovy-purple) !important;
        color: white !important;
        font-weight: 600;
    }

    .main-canvas {
        flex-grow: 1;
        padding: 40px 60px;
    }

    .stats-layout {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
        font-weight: 700;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .activity-item {
        background: var(--card-bg);
        padding: 15px 20px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        border: 1px solid #231633;
        margin-bottom: 12px;
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

    .watchlist-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 30px;
    }

    .poster-container {
        width: 100%;
        aspect-ratio: 2/3;
        border-radius: 15px;
        overflow: hidden;
        background: #1a1026;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .movie-poster-wrapper:hover .poster-container {
        transform: translateY(-8px);
        border-color: var(--moovy-purple);
        box-shadow: 0 12px 20px rgba(209, 106, 255, 0.25);
    }

    .poster-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .movie-title {
        margin-top: 12px;
        font-weight: 700;
        font-size: 14px;
        line-height: 1.2;
        color: white;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-transform: uppercase;
    }

    .movie-genre-tag {
        font-size: 11px;
        color: var(--text-gray);
        margin-top: 4px;
        font-weight: 500;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(6, 3, 10, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(15px);
    }

    .modal-content {
        background: var(--card-bg);
        width: 90%;
        max-width: 480px;
        max-height: 75vh;
        border-radius: 28px;
        border: 1px solid rgba(209, 106, 255, 0.3);
        padding: 30px;
        display: flex;
        flex-direction: column;
    }

    .modal-movie-item {
        display: flex;
        align-items: center;
        padding: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 18px;
        margin-bottom: 10px;
        transition: 0.2s;
    }

    .modal-movie-item img {
        width: 48px;
        height: 64px;
        border-radius: 10px;
        margin-right: 15px;
        object-fit: cover;
    }

    .btn-toggle-watchlist {
        margin-left: auto;
        background: transparent;
        border: 1.5px solid var(--moovy-purple);
        color: var(--moovy-purple);
        padding: 7px 18px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-toggle-watchlist.is-added {
        background: var(--moovy-purple);
        color: white;
    }
</style>

<div class="dashboard-wrapper">
    <nav class="sidebar-nav">
        <div class="profile-section">
            <div class="avatar-circle">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <h4 style="margin-bottom: 5px; font-weight: 700;">{{ Auth::user()->name }}</h4>
            <p style="color: var(--moovy-purple); font-size: 10px; font-weight: 800; letter-spacing: 1.5px;">PLATINUM
                MEMBER</p>
        </div>

        <div class="menu-container">
            <a href="#" class="menu-link active">👤 Profile</a>
            <a href="#recent-activity" class="menu-link">🕒 Recent Activity</a>
            <a href="#watchlist-section" class="menu-link">⭐ Watchlist</a>
        </div>
    </nav>

    <main class="main-canvas">
    <div class="stats-layout">
        <div class="stat-card">
            <h2>{{ $stats['watched'] }}</h2>
            <p style="color: var(--text-gray); font-size: 11px; font-weight: 600; margin-top: 8px;">WATCHED</p>
        </div>
        <div class="stat-card">
            <h2>{{ $stats['saved'] }}</h2>
            <p style="color: var(--text-gray); font-size: 11px; font-weight: 600; margin-top: 8px;">WATCHLIST</p>
        </div>
        <div class="stat-card">
            <h2>{{ $stats['rating'] }}</h2>
            <p style="color: var(--text-gray); font-size: 11px; font-weight: 600; margin-top: 8px;">AVG RATING</p>
        </div>
    </div>

    <div id="recent-activity" style="margin-bottom: 50px;">
        <div class="section-header">
            <span class="section-title">Recent Activities</span>
            <a href="#" class="view-link" style="color: var(--moovy-purple); text-decoration: none; font-size: 13px; font-weight: 600;">View All</a>
        </div>
        <div class="watchlist-grid">
            @forelse($recentTickets as $ticket)
                <div class="movie-poster-wrapper">
                    <div class="poster-container">
                        <img src="{{ $ticket->movie->poster ? asset('storage/' . $ticket->movie->poster) : asset('images/no-poster.jpg') }}" class="poster-image">
                    </div>
                    <div class="movie-title">{{ $ticket->movie->title }}</div>
                    <div class="movie-genre-tag">
                        {{ $ticket->movie->genre }} • {{ $ticket->created_at->format('M d') }}
                    </div>
                </div>
            @empty
                <p style="color: var(--text-gray); font-size: 14px; grid-column: 1/-1;">No recent activities found.</p>
            @endforelse
        </div>
    </div>

    <div id="watchlist-section">
        <div class="section-header">
            <span class="section-title">My Watchlist</span>
        </div>
        <div class="watchlist-grid">
            @foreach($watchlistMovies as $movie)
                <div class="movie-poster-wrapper">
                    <div class="poster-container">
                        <img src="{{ $movie->poster ? asset('storage/' . $movie->poster) : asset('images/no-poster.jpg') }}" class="poster-image">
                    </div>
                    <div class="movie-title">{{ $movie->title }}</div>
                    <div class="movie-genre-tag">{{ $movie->genre }}</div>
                </div>
            @endforeach

            <div class="movie-poster-wrapper" onclick="toggleModal(true)">
                <div class="poster-container" style="border: 2px dashed #3d2b52; background: rgba(209, 106, 255, 0.02); display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer;">
                    <div style="font-size: 32px; color: var(--moovy-purple);">+</div>
                    <div style="font-size: 12px; font-weight: 800; color: var(--moovy-purple); margin-top: 8px; text-transform: uppercase;">Add More</div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>

<script>
    function toggleModal(show) {
        const modal = document.getElementById('movieModal');
        modal.style.display = show ? 'flex' : 'none';
        if (show) loadMoviesForManagement();
        else location.reload();
    }

    function loadMoviesForManagement() {
        const list = document.getElementById('modalList');
        fetch("/api/movies-status", { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                let html = '';
                data.forEach((movie, index) => {
                    const isAdded = movie.is_added;
                    const btnClass = isAdded ? 'is-added' : '';
                    const btnText = isAdded ? 'Remove' : 'Add';
                    html += `
                                        <div class="modal-movie-item" style="animation: slideUp 0.3s ease forwards; animation-delay: ${index * 0.05}s; opacity: 0;">
                                            <img src="${movie.poster_url || '/images/no-poster.jpg'}">
                                            <div style="flex: 1;">
                                                <div style="font-size: 14px; font-weight: 600; color: white;">${movie.title}</div>
                                                <div style="font-size: 11px; color: var(--text-gray);">${movie.genre}</div>
                                            </div>
                                            <button class="btn-toggle-watchlist ${btnClass}" onclick="handleToggle(${movie.id}, this)">
                                                ${btnText}
                                            </button>
                                        </div>
                                    `;
                });
                list.innerHTML = html || '<p style="text-align:center; color: var(--text-gray);">No movies available.</p>';
            })
            .catch(() => {
                list.innerHTML = '<p style="text-align:center; color:#ff4d4d;">Error loading movies.</p>';
            });
    }

    function handleToggle(movieId, btn) {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        fetch(`/watchlist/toggle/${movieId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
            .then(res => res.json())
            .then(() => {
                btn.disabled = false;
                btn.style.opacity = '1';
                if (btn.classList.contains('is-added')) {
                    btn.classList.remove('is-added');
                    btn.innerText = 'Add';
                } else {
                    btn.classList.add('is-added');
                    btn.innerText = 'Remove';
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
    }

    const style = document.createElement('style');
    style.innerHTML = `@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }`;
    document.head.appendChild(style);
</script>
@endsection