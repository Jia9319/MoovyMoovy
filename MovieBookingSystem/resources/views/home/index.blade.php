@extends('layouts.app')

@section('title', 'MoovyMoovy — Book Your Experience')

@section('content')

{{-- Hero Slider --}}
<div class="hero-slider" id="heroSlider">
    <div class="slider-container">
        <div class="main-slide" id="mainSlide">
            <div class="slide-bg" id="slideBg"></div>
            <div class="slide-content">
                <div class="movie-poster-large" id="moviePoster">
                    <div class="poster-gradient"></div>
                    <div class="poster-info">
                        <div class="movie-badge" id="movieBadge">Now Showing</div>
                        <h1 class="movie-title" id="movieTitle"></h1>
                        <div class="movie-meta" id="movieMeta"></div>
                        <p class="movie-description" id="movieDesc"></p>
                        <div class="movie-actions">
                            <button class="btn-book" id="heroBookBtn">Book Tickets</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="slider-nav prev" id="prevSlide">
            <svg width="40" height="16" viewBox="0 0 40 16" fill="none">
                <line x1="39" y1="8" x2="1" y2="8" stroke="white" stroke-width="1.5"/>
                <polyline points="10,1 1,8 10,15" fill="none" stroke="white" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="next-preview" id="nextPreview">
            <div class="np-left">
                <span class="np-text">next</span>
                <div class="next-preview-title" id="nextPreviewTitle"></div>
                <svg class="np-arrow-svg" width="52" height="14" viewBox="0 0 52 14" fill="none">
                    <line x1="0" y1="7" x2="43" y2="7" stroke="white" stroke-width="1.2"/>
                    <polyline points="34,1 43,7 34,13" fill="none" stroke="white" stroke-width="1.2" stroke-linejoin="round" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="next-preview-thumb">
                <div class="next-thumb-inner" id="nextThumbInner"></div>
                <div class="next-play-overlay">
                    <div class="next-play-btn">
                        <svg width="12" height="14" viewBox="0 0 12 14" fill="white"><path d="M0 0l12 7-12 7V0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="progress-indicator"><div class="progress-bar" id="progressBar"></div></div>
        <div class="slide-counter"><span id="currentSlide">01</span> / <span id="totalSlides">01</span></div>
    </div>
</div>

{{-- Now Showing + Promo --}}
<div class="rest-content">
    <section style="padding:3rem clamp(1rem,5vw,3.25rem) 4.5rem;">
        <div class="sec-head">
            <h2 class="sec-title">Now <span class="acc">Showing</span></h2>
            <a href="{{ url('/movies') }}" class="sec-more">View All →</a>
        </div>

        @if($nowShowing->isEmpty())
        <div class="empty-state">
            <i class="fas fa-film"></i>
            <p>No movies yet.</p>
            @auth
            <a href="{{ route('movies.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i> Add First Movie
            </a>
            @endauth
        </div>
        @else
        <div class="movies-grid">
            @foreach($nowShowing as $movie)
            <div class="mcard" onclick="location.href='{{ route('movies.show', $movie->id) }}'">
                <div class="mthumb">
                    @if($movie->poster)
                        <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}">
                    @else
                        <div class="mthumb-bg" style="position:absolute;inset:0;background:{{ $movie->gradient }};"></div>
                        <div class="mthumb-title">{{ strtoupper($movie->title) }}</div>
                    @endif
                    <div class="moverlay">
                        <button class="mbtn">Book Ticket</button>
                    </div>
                </div>
                <span class="genre-badge">{{ $movie->genre }}</span>
                <div class="minfo">
                    <div class="mname">{{ $movie->title }}</div>
                    <div class="mmeta">
                        <div class="mrating">★ {{ $movie->rating ?? 'N/A' }}</div>
                        <div class="mdur">{{ $movie->duration }}min</div>
                    </div>
                    <div class="mprice">
                        @if($movie->showtimes->isNotEmpty())
                            From RM {{ number_format($movie->showtimes->min('price'), 2) }}
                        @else
                            <span style="color:var(--muted);font-size:0.8rem;">No showtimes yet</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>

    <div class="promo">
        <div class="promo-text">
            <div class="promo-ey">🎉 Limited Time Offer</div>
            <div class="promo-h">STUDENT TUESDAYS<br>— 50% OFF</div>
            <p class="promo-d">Flash your student ID every Tuesday and get half-price tickets to any screening. Valid at all MoovyMoovy locations across Malaysia.</p>
        </div>
        <button class="promo-btn">Claim Offer →</button>
    </div>
</div>

@push('scripts')
<script>

const movies = {!! json_encode($moviesJson) !!};

if (!movies.length) {
    document.getElementById('heroSlider').style.display = 'none';
} else {
    let cur = 0, autoPlay, progInt;
    const DELAY = 5000;

    const slideBg   = document.getElementById('slideBg');
    const mTitle    = document.getElementById('movieTitle');
    const mMeta     = document.getElementById('movieMeta');
    const mDesc     = document.getElementById('movieDesc');
    const curSpan   = document.getElementById('currentSlide');
    const totSpan   = document.getElementById('totalSlides');
    const progBar   = document.getElementById('progressBar');
    const nextPrev  = document.getElementById('nextPreview');
    const nextTitle = document.getElementById('nextPreviewTitle');
    const nextThumb = document.getElementById('nextThumbInner');
    const bookBtn   = document.getElementById('heroBookBtn');

    totSpan.textContent = String(movies.length).padStart(2, '0');

    function setBg(el, m) {
        if (m.poster) {
            el.style.background = "url('" + m.poster + "') center/cover no-repeat";
            el.style.filter     = 'brightness(0.35)';
        } else {
            el.style.background = m.bgGradient;
            el.style.filter     = '';
        }
    }

    function updateSlide() {
        const m = movies[cur];

        setBg(slideBg, m);

        const el = document.querySelector('.movie-poster-large');
        el.style.animation = 'none';
        el.offsetHeight;
        el.style.animation = 'fadeInUp 0.5s ease';

        mTitle.textContent  = m.title;
        mDesc.textContent   = m.description;
        bookBtn.onclick     = function() { location.href = m.url; };
        curSpan.textContent = String(cur + 1).padStart(2, '0');

        var metaHtml = '<span class="rating">\u2605 ' + m.rating + '</span>'
            + '<span class="dot">&bull;</span><span>' + m.duration + '</span>'
            + '<span class="dot">&bull;</span><span>' + m.genre + '</span>'
            + '<span class="dot">&bull;</span><span>' + m.year + '</span>';
        if (m.price) {
            metaHtml += '<span class="dot">&bull;</span><span style="color:var(--c1);font-weight:600;">' + m.price + '</span>';
        }
        mMeta.innerHTML = metaHtml;

        const next = movies[(cur + 1) % movies.length];
        nextTitle.textContent = next.title;
        setBg(nextThumb, next);

        resetProg();
    }

    function nextSlide() { cur = (cur + 1) % movies.length;                updateSlide(); resetAuto(); }
    function prevSlide() { cur = (cur - 1 + movies.length) % movies.length; updateSlide(); resetAuto(); }

    nextPrev.addEventListener('click', nextSlide);
    document.getElementById('prevSlide').addEventListener('click', prevSlide);

    function startProg() {
        clearInterval(progInt);
        progBar.style.width = '0%';
        var w = 0;
        progInt = setInterval(function() {
            if (w >= 100) clearInterval(progInt);
            else progBar.style.width = (++w) + '%';
        }, DELAY / 100);
    }
    function resetProg() { clearInterval(progInt); startProg(); }
    function startAuto() { clearInterval(autoPlay); autoPlay = setInterval(nextSlide, DELAY); startProg(); }
    function resetAuto() { clearInterval(autoPlay); startAuto(); }
    function stopAuto()  { clearInterval(autoPlay); autoPlay = null; clearInterval(progInt); }

    document.querySelector('.hero-slider').addEventListener('mouseenter', stopAuto);
    document.querySelector('.hero-slider').addEventListener('mouseleave', startAuto);

    updateSlide();
    startAuto();
}
</script>
@endpush
@endsection