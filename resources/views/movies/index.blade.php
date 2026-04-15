@extends('layouts.app')

@section('title', 'Now Showing - MoovyMoovy')

@section('content')

<section style="padding:120px 5% 4rem;">
    <div class="sec-head" style="margin-bottom:2rem;">
        <h1 class="sec-title" style="font-size:3rem;">Now <span class="acc">Showing</span></h1>

        <form method="GET" action="{{ route('movies.index') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
            <div style="display:flex;align-items:center;background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search movies..."
                    style="background:transparent;border:none;padding:0.75rem 1rem;color:white;width:220px;outline:none;">
                <button type="submit" style="background:var(--grad-2);border:none;padding:0.75rem 1rem;cursor:pointer;color:white;">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <select name="genre" onchange="this.form.submit()"
                style="background:var(--card);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:12px;outline:none;">
                <option value="">All Genres</option>
                @foreach(['Action','Sci-Fi','Drama','Comedy','Horror','Superhero','Thriller','Romance','Animation','Documentary'] as $g)
                    <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>

            <select name="sort" onchange="this.form.submit()"
                style="background:var(--card);border:1px solid var(--border);color:white;padding:0.75rem 1rem;border-radius:12px;outline:none;">
                <option value="">Sort: Latest</option>
                <option value="rating"  {{ request('sort') == 'rating'  ? 'selected' : '' }}>Rating: High to Low</option>
                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
            </select>

            @if(request('search') || request('genre') || request('sort'))
            <a href="{{ route('movies.index') }}" style="color:var(--muted);font-size:0.875rem;text-decoration:none;">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </form>
    </div>

    @if($movies->isEmpty())
    <div style="text-align:center;padding:5rem 2rem;color:var(--muted);">
        <i class="fas fa-film" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:0.4;"></i>
        <p style="margin-bottom:1rem;">No movies found.</p>
        @auth
        <a href="{{ route('movies.create') }}" style="background:var(--grad-2);color:white;padding:0.75rem 1.5rem;border-radius:8px;text-decoration:none;">
            <i class="fas fa-plus"></i> Add First Movie
        </a>
        @endauth
    </div>
    @else
    <div class="movies-grid">
        @foreach($movies as $movie)
        <div class="mcard" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
            <div class="mthumb">
                @if($movie->poster)
                    <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}">
                @else
                    <div class="mthumb-title">{{ strtoupper($movie->title) }}</div>
                @endif
                <div class="moverlay">
                    <button class="mbtn">View Details</button>
                </div>
            </div>

            <div class="minfo">
                <div class="mname">{{ Str::limit($movie->title, 35) }}</div>
                <div class="genre-badge">{{ $movie->genre }}</div>
                <div class="mmeta">
                    <div class="mrating">★ {{ $movie->rating ?? '0.0' }}</div>
                    <div class="mdur">{{ $movie->duration }} min</div>
                </div>
                <div class="mprice">
                    From RM {{ number_format($movie->showtimes->min('price') ?? 0, 2) }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Simple Pagination - Only page numbers -->
    @if($movies->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination">
            {{-- Previous --}}
            @if($movies->onFirstPage())
                <span class="disabled">&laquo;</span>
            @else
                <a href="{{ $movies->previousPageUrl() }}">&laquo;</a>
            @endif

            {{-- Page Numbers --}}
            @foreach($movies->getUrlRange(1, $movies->lastPage()) as $page => $url)
                @if($page == $movies->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($movies->hasMorePages())
                <a href="{{ $movies->nextPageUrl() }}">&raquo;</a>
            @else
                <span class="disabled">&raquo;</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</section>



@endsection