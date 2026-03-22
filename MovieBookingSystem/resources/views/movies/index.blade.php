@extends('layouts.app')

@section('title', 'Now Showing - MoovyMoovy')

@section('content')
<section style="padding:120px 5% 4rem;">
    <div class="sec-head" style="margin-bottom:2rem;">
        <h1 class="sec-title" style="font-size:3rem;">Now <span class="acc">Showing</span></h1>

        {{-- Search & Filter form — submits to same page --}}
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
    <div class="movies-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.5rem;">
        @foreach($movies as $movie)
        <div class="mcard" style="background:var(--card);border-radius:16px;overflow:hidden;border:1px solid var(--border);transition:all 0.3s;cursor:pointer;"
             onclick="location.href='{{ route('movies.show', $movie->id) }}'">

            {{-- Poster --}}
            <div class="mthumb" style="height:280px;position:relative;overflow:hidden;background:linear-gradient(145deg,#1a0033,#660094);display:flex;align-items:center;justify-content:center;">
                @if($movie->poster)
                    <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}"
                         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
                @else
                    <div style="font-family:'Bebas Neue';font-size:1.4rem;text-align:center;padding:1rem;line-height:1.2;z-index:1;">
                        {{ strtoupper($movie->title) }}
                    </div>
                @endif
                <div class="moverlay" style="position:absolute;inset:0;background:rgba(0,0,0,0.8);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.3s;z-index:3;">
                    <button class="mbtn" style="background:var(--grad-2);border:none;color:white;padding:0.75rem 1.5rem;border-radius:8px;cursor:pointer;">
                        View Details
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <div class="minfo" style="padding:1rem;">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:0.4rem;gap:0.5rem;">
                    <div class="mname" style="font-weight:600;font-size:0.95rem;line-height:1.3;">{{ $movie->title }}</div>
                    <span style="background:rgba(209,106,255,0.15);color:var(--c1);font-size:0.7rem;padding:0.2rem 0.5rem;border-radius:6px;white-space:nowrap;flex-shrink:0;">
                        {{ $movie->genre }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                    <div style="color:#ffc107;font-size:0.875rem;">★ {{ $movie->rating ?? 'N/A' }}</div>
                    <div style="color:var(--muted);font-size:0.875rem;">{{ $movie->duration }} min</div>
                </div>
                <div style="color:var(--c1);font-size:0.875rem;">
                    From RM {{ $movie->showtimes->min('price') ?? '—' }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div style="display:flex;justify-content:center;margin-top:3rem;">
        {{ $movies->links() }}
    </div>
    @endif
</section>

<style>
.mcard:hover { transform:translateY(-8px);border-color:var(--c1) !important;box-shadow:0 20px 40px rgba(102,0,148,0.4); }
.mcard:hover .moverlay { opacity:1 !important; }
</style>
@endsection