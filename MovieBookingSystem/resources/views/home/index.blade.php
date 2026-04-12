@extends('layouts.app')

@section('title', 'MoovyMoovy — Book Your Experience')

@section('content')

<div class="moovy-hero-slider" id="moovyHeroSlider">
    <div class="moovy-slider-container">
        <div class="moovy-slide-main" id="moovySlideMain">
            <!-- <div class="moovy-slide-background" id="moovySlideBg"></div> -->
            
            <div class="moovy-gradient-blend"></div>
            
            <div class="moovy-slide-content-wrapper">
                <div class="moovy-poster-card" id="moovyPosterCard">
                    <div class="moovy-poster-info">
                        <div class="moovy-badge" id="moovyBadge">Now Showing</div>
                        <h1 class="moovy-title" id="moovyTitle"></h1>
                        <div class="moovy-meta" id="moovyMeta"></div>
                        <p class="moovy-description" id="moovyDesc"></p>
                        <div class="moovy-actions">
                            <a id="moovyBookBtn" href="#" class="moovy-btn-book">Book Tickets</a>
                            <button class="moovy-btn-trailer">
                                <i class="fas fa-play"></i> Trailer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="moovy-nav-prev" id="moovyPrevSlide">
            <svg width="40" height="16" viewBox="0 0 40 16" fill="none">
                <line x1="39" y1="8" x2="1" y2="8" stroke="white" stroke-width="1.5"/>
                <polyline points="10,1 1,8 10,15" fill="none" stroke="white" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="moovy-next-preview" id="moovyNextPreview">
            <div class="moovy-np-left">
                <span class="moovy-np-text">next</span>
                <div class="moovy-np-title" id="moovyNextTitle"></div>
                <svg class="moovy-np-arrow" width="52" height="14" viewBox="0 0 52 14" fill="none">
                    <line x1="0" y1="7" x2="43" y2="7" stroke="white" stroke-width="1.2"/>
                    <polyline points="34,1 43,7 34,13" fill="none" stroke="white" stroke-width="1.2" stroke-linejoin="round" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="moovy-np-thumb">
                <div class="moovy-np-thumb-inner" id="moovyNextThumb"></div>
                <div class="moovy-np-overlay">
                    <div class="moovy-np-play-btn">
                        <svg width="12" height="14" viewBox="0 0 12 14" fill="white"><path d="M0 0l12 7-12 7V0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="moovy-progress-bar"><div class="moovy-progress-fill" id="moovyProgressFill"></div></div>
        <div class="moovy-slide-counter"><span id="moovyCurrentSlide">01</span> / <span id="moovyTotalSlides">01</span></div>
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
<style>


.moovy-hero-slider {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
    background: #0a0010;
}

.moovy-slider-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.moovy-slide-main {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.moovy-slide-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: contain !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    transition: transform 0.6s ease;
    z-index: 1;
    background-color: #0a0010;
}

.moovy-gradient-blend {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 2;
    pointer-events: none;
    background: radial-gradient(
        ellipse 70% 60% at 30% 50%,
        transparent 0%,
        transparent 25%,
        rgba(10, 0, 16, 0.2) 45%,
        rgba(10, 0, 16, 0.5) 60%,
        rgba(10, 0, 16, 0.8) 75%,
        rgba(10, 0, 16, 0.95) 100%
    );
}

.moovy-slide-content-wrapper {
    position: relative;
    z-index: 10;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    padding: 0 5% 0 8%;
}

.moovy-poster-card {
    position: relative;
    max-width: 550px;
    width: 100%;
    animation: moovyFadeInUp 0.8s ease;
}

@keyframes moovyFadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.moovy-poster-info {
    position: relative;
}

.moovy-badge {
    display: inline-block;
    background: rgba(209, 106, 255, 0.2);
    border: 1px solid #d16aff;
    color: #d16aff;
    padding: 0.35rem 1.25rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 1rem;
    letter-spacing: 1px;
    backdrop-filter: blur(5px);
}

.moovy-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.5rem, 8vw, 5rem);
    line-height: 1;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #d16aff, #9614d0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.moovy-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.875rem;
}

.moovy-meta .rating {
    color: #ffc107;
    font-weight: 600;
}

.moovy-meta .dot {
    color: rgba(255, 255, 255, 0.1);
}

.moovy-description {
    color: rgba(255, 255, 255, 0.55);
    line-height: 1.7;
    margin-bottom: 2rem;
    font-size: 0.95rem;
    max-width: 90%;
}

.moovy-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.moovy-btn-book {
    background: linear-gradient(135deg, #9614d0, #660094);
    color: white;
    border: none;
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.9375rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.moovy-btn-book:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(150, 20, 208, 0.5);
}

.moovy-btn-trailer {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.moovy-btn-trailer:hover {
    background: rgba(209, 106, 255, 0.15);
    border-color: #d16aff;
    transform: translateY(-2px);
}

.moovy-nav-prev {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    color: white;
    cursor: pointer;
    z-index: 20;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    left: 2rem;
}

.moovy-nav-prev:hover {
    background: #d16aff;
    border-color: #d16aff;
    transform: translateY(-50%) scale(1.1);
}

.moovy-next-preview {
    position: absolute;
    top: 50%;
    right: 3rem;
    transform: translateY(-50%);
    z-index: 20;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.moovy-next-preview:hover {
    transform: translateY(-50%) translateX(-8px);
    opacity: 0.95;
}

.moovy-np-left {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
    padding-right: 16px;
}

.moovy-np-text {
    font-size: 11px;
    letter-spacing: 2px;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 400;
    text-transform: lowercase;
}

.moovy-np-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 16px;
    letter-spacing: 2px;
    color: #fff;
    line-height: 1.2;
    text-align: right;
    max-width: 130px;
}

.moovy-np-arrow {
    display: block;
    opacity: 0.7;
    transition: opacity 0.2s, transform 0.2s;
}

.moovy-next-preview:hover .moovy-np-arrow {
    opacity: 1;
    transform: translateX(4px);
}

.moovy-np-thumb {
    position: relative;
    width: 240px;
    height: 160px;
    overflow: hidden;
    flex-shrink: 0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.moovy-np-thumb-inner {
    position: absolute;
    inset: 0;
    transition: transform 0.4s ease;
    background-size: cover !important;
    background-position: center !important;
}

.moovy-next-preview:hover .moovy-np-thumb-inner {
    transform: scale(1.05);
}

.moovy-np-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}

.moovy-np-play-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.65);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, background 0.2s, border-color 0.2s;
}

.moovy-next-preview:hover .moovy-np-play-btn {
    transform: scale(1.1);
    background: #d16aff;
    border-color: #d16aff;
}

.moovy-progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgba(255, 255, 255, 0.15);
    z-index: 20;
}

.moovy-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #d16aff, #9614d0);
    width: 0%;
    transition: width 0.3s linear;
}

.moovy-slide-counter {
    position: absolute;
    bottom: 2rem;
    right: 2rem;
    z-index: 20;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.875rem;
    color: white;
    font-weight: 500;
    letter-spacing: 1px;
    font-family: monospace;
}

/* Responsive */
@media (max-width: 1024px) {
    .moovy-slide-content-wrapper {
        padding-left: 6%;
    }
    .moovy-poster-card {
        max-width: 480px;
    }
    .moovy-gradient-blend {
        background: radial-gradient(
            ellipse 70% 60% at 30% 50%,
            transparent 0%,
            transparent 20%,
            rgba(10, 0, 16, 0.3) 40%,
            rgba(10, 0, 16, 0.6) 55%,
            rgba(10, 0, 16, 0.85) 70%,
            rgba(10, 0, 16, 0.95) 100%
        );
    }
}

@media (max-width: 768px) {
    .moovy-next-preview {
        display: none;
    }
    .moovy-slide-content-wrapper {
        padding: 0 5%;
        align-items: flex-end;
        padding-bottom: 100px;
    }
    .moovy-poster-card {
        max-width: 100%;
        text-align: center;
    }
    .moovy-meta {
        justify-content: center;
    }
    .moovy-description {
        max-width: 100%;
    }
    .moovy-actions {
        justify-content: center;
    }
    .moovy-nav-prev {
        left: 1rem;
        width: 40px;
        height: 40px;
    }
    .moovy-gradient-blend {
        background: radial-gradient(
            ellipse at center,
            transparent 0%,
            rgba(10, 0, 16, 0.3) 30%,
            rgba(10, 0, 16, 0.6) 50%,
            rgba(10, 0, 16, 0.85) 70%,
            rgba(10, 0, 16, 0.95) 100%
        );
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const movies = {!! json_encode($moviesJson) !!};
    
    console.log('Movies data:', movies);

    if (!movies || movies.length === 0) {
        const heroSlider = document.getElementById('moovyHeroSlider');
        if (heroSlider) heroSlider.style.display = 'none';
        return;
    }

    let currentIndex = 0;
    let autoPlayInterval;
    let progressInterval;
    const AUTO_DELAY = 6000;

    const slideBg = document.getElementById('moovySlideBg');
    const movieTitle = document.getElementById('moovyTitle');
    const movieMeta = document.getElementById('moovyMeta');
    const movieDesc = document.getElementById('moovyDesc');
    const currentSlideSpan = document.getElementById('moovyCurrentSlide');
    const totalSlidesSpan = document.getElementById('moovyTotalSlides');
    const progressFill = document.getElementById('moovyProgressFill');
    const nextPreview = document.getElementById('moovyNextPreview');
    const nextTitle = document.getElementById('moovyNextTitle');
    const nextThumb = document.getElementById('moovyNextThumb');
    const bookBtn = document.getElementById('moovyBookBtn');
    const prevBtn = document.getElementById('moovyPrevSlide');

    if (totalSlidesSpan) {
        totalSlidesSpan.textContent = String(movies.length).padStart(2, '0');
    }

    function setBackground(element, movie) {
        if (!element) return;
        
        const posterUrl = movie.poster || movie.poster_url || movie.image;
        
        if (posterUrl) {
            element.style.background = "url('" + posterUrl + "') center center/contain no-repeat";
            element.style.backgroundColor = '#0a0010';
        } else if (movie.bgGradient) {
            element.style.background = movie.bgGradient;
        } else {
            element.style.background = 'linear-gradient(135deg, #1a0033, #660094)';
        }
    }

    // Update slide
    function updateSlide() {
        const movie = movies[currentIndex];
        if (!movie) return;

        setBackground(slideBg, movie);

        const posterCard = document.querySelector('.moovy-poster-card');
        if (posterCard) {
            posterCard.style.animation = 'none';
            posterCard.offsetHeight;
            posterCard.style.animation = 'moovyFadeInUp 0.5s ease';
        }

        if (movieTitle) movieTitle.textContent = movie.title || 'Untitled';
        if (movieDesc) movieDesc.textContent = movie.description || 'No description available';
        if (currentSlideSpan) currentSlideSpan.textContent = String(currentIndex + 1).padStart(2, '0');

        if (bookBtn && movie.url) {
            bookBtn.href = movie.url;
        }

        if (movieMeta) {
            let metaHtml = '<span class="rating">★ ' + (movie.rating || 'N/A') + '</span>' +
                '<span class="dot">•</span><span>' + (movie.duration || 'N/A') + '</span>' +
                '<span class="dot">•</span><span>' + (movie.genre || 'N/A') + '</span>' +
                '<span class="dot">•</span><span>' + (movie.year || 'N/A') + '</span>';
            if (movie.price) {
                metaHtml += '<span class="dot">•</span><span style="color:#d16aff;font-weight:600;">' + movie.price + '</span>';
            }
            movieMeta.innerHTML = metaHtml;
        }

        // Update next preview
        const nextMovie = movies[(currentIndex + 1) % movies.length];
        if (nextTitle && nextMovie) {
            nextTitle.textContent = nextMovie.title || '';
        }
        if (nextThumb && nextMovie) {
            const posterUrl = nextMovie.poster || nextMovie.poster_url || nextMovie.image;
            if (posterUrl) {
                nextThumb.style.background = "url('" + posterUrl + "') center center/cover no-repeat";
            }
        }

        resetProgress();
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % movies.length;
        updateSlide();
        resetAutoPlay();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + movies.length) % movies.length;
        updateSlide();
        resetAutoPlay();
    }

    function startProgress() {
        if (progressInterval) clearInterval(progressInterval);
        if (!progressFill) return;
        progressFill.style.width = '0%';
        let width = 0;
        progressInterval = setInterval(() => {
            if (width >= 100) {
                clearInterval(progressInterval);
            } else {
                width++;
                progressFill.style.width = width + '%';
            }
        }, AUTO_DELAY / 100);
    }

    function resetProgress() {
        clearInterval(progressInterval);
        startProgress();
    }

    function startAutoPlay() {
        if (autoPlayInterval) clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(nextSlide, AUTO_DELAY);
        startProgress();
    }

    function resetAutoPlay() {
        clearInterval(autoPlayInterval);
        startAutoPlay();
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
        clearInterval(progressInterval);
    }

    if (nextPreview) nextPreview.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    const slider = document.querySelector('.moovy-hero-slider');
    if (slider) {
        slider.addEventListener('mouseenter', stopAutoPlay);
        slider.addEventListener('mouseleave', startAutoPlay);
    }

    document.querySelectorAll('.moovy-btn-trailer').forEach(btn => {
        btn.addEventListener('click', () => alert('🎬 Trailer coming soon!'));
    });

    updateSlide();
    startAutoPlay();
});
</script>
@endpush
@endsection