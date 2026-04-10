@extends('layouts.app')

@section('title', 'Home - Book Your Experience')

@section('content')
<div class="hero-slider" id="heroSlider">
    <div class="slider-container">
        <div class="main-slide" id="mainSlide">
            <div class="slide-bg" id="slideBg"></div>
            <div class="slide-content">
                <div class="movie-poster-large" id="moviePoster">
                    <div class="poster-gradient"></div>
                    <div class="poster-info">
                        <div class="movie-badge" id="movieBadge">Now Showing</div>
                        <h1 class="movie-title" id="movieTitle">DUNE: PART TWO</h1>
                        <div class="movie-meta" id="movieMeta">
                            <span class="rating">★ 8.5</span>
                            <span class="dot">•</span>
                            <span>2h 46m</span>
                            <span class="dot">•</span>
                            <span>Sci-Fi</span>
                            <span class="dot">•</span>
                            <span>2024</span>
                        </div>
                        <p class="movie-description" id="movieDesc">
                            Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.
                        </p>
                        <div class="movie-actions">
                            <a id="heroBookBtn" href="{{ route('booking.select', ['movie_id' => 1, 'title' => 'DUNE: PART TWO', 'genre' => 'Sci-Fi', 'duration' => 166]) }}" class="btn-book" style="text-decoration: none; display: inline-block;">Book Tickets</a>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="slider-nav prev" id="prevSlide">
            <svg width="40" height="16" viewBox="0 0 40 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="39" y1="8" x2="1" y2="8" stroke="white" stroke-width="1.5"/>
                <polyline points="10,1 1,8 10,15" fill="none" stroke="white" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="next-preview" id="nextPreview" title="Click to see next movie">
            <div class="np-left">
                <span class="np-text">next</span>
                <div class="next-preview-title" id="nextPreviewTitle">THE BATMAN 2</div>
                <svg class="np-arrow-svg" width="52" height="14" viewBox="0 0 52 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <line x1="0" y1="7" x2="43" y2="7" stroke="white" stroke-width="1.2"/>
                    <polyline points="34,1 43,7 34,13" fill="none" stroke="white" stroke-width="1.2" stroke-linejoin="round" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="next-preview-thumb">
                <div class="next-thumb-inner" id="nextThumbInner"
                     style="background: linear-gradient(145deg,#200044,#9614d0);"></div>
                <div class="next-play-overlay">
                    <div class="next-play-btn">
                        <svg width="12" height="14" viewBox="0 0 12 14" fill="white">
                            <path d="M0 0l12 7-12 7V0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="progress-indicator">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <div class="slide-counter">
            <span id="currentSlide">01</span> / <span id="totalSlides">06</span>
        </div>
    </div>
</div>

<div class="rest-content">
    <div class="featured-strip" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,300px),1fr));gap:1.375rem;padding:0 clamp(1rem,5vw,3.25rem);margin-bottom:4.5rem;margin-top:2rem;">
        <div class="fcard">
            <div class="fcard-bg" style="background:linear-gradient(145deg,#1a0033,#660094);"></div>
            <div class="fcard-grad"></div>
            <div class="fcard-body">
                <span class="fcard-tag">Featured</span>
                <div class="fcard-title">DUNE: PART TWO</div>
                <div class="fcard-meta"><span class="rat">★ 8.5</span><span>2h 46m</span><span>Sci-Fi</span></div>
                <a class="fcard-btn" style="text-decoration: none; display: inline-block;" href="{{ route('booking.select', ['movie_id' => 1, 'title' => 'DUNE: PART TWO', 'genre' => 'Sci-Fi', 'duration' => 166]) }}">Book Ticket</a>
            </div>
        </div>
        <div class="fcard">
            <div class="fcard-bg" style="background:linear-gradient(145deg,#200044,#9614d0);"></div>
            <div class="fcard-grad"></div>
            <div class="fcard-body sm">
                <span class="fcard-tag">Trending</span>
                <div class="fcard-title sm">THE BATMAN 2</div>
                <div class="fcard-meta"><span class="rat">★ 8.6</span><span>2h 55m</span></div>
                <a class="fcard-btn" style="text-decoration: none; display: inline-block;" href="{{ route('booking.select', ['movie_id' => 3, 'title' => 'THE BATMAN 2', 'genre' => 'Superhero', 'duration' => 175]) }}">Book Ticket</a>
            </div>
        </div>
        <div class="fcard">
            <div class="fcard-bg" style="background:linear-gradient(145deg,#100020,#bb44f0);"></div>
            <div class="fcard-grad"></div>
            <div class="fcard-body sm">
                <span class="fcard-tag">New</span>
                <div class="fcard-title sm">SPIDER-MAN 4</div>
                <div class="fcard-meta"><span class="rat">★ 8.7</span><span>2h 20m</span></div>
                <a class="fcard-btn" style="text-decoration: none; display: inline-block;" href="{{ route('booking.select', ['movie_id' => 4, 'title' => 'SPIDER-MAN 4', 'genre' => 'Superhero', 'duration' => 140]) }}">Book Ticket</a>
            </div>
        </div>
    </div>

    <section style="padding:0 clamp(1rem,5vw,3.25rem) 4.5rem;">
        <div class="sec-head">
            <h2 class="sec-title">Now <span class="acc">Showing</span></h2>
            <a href="{{ url('/movies') }}" class="sec-more">View All →</a>
        </div>
        <div class="movies-grid">
            @for($i = 1; $i <= 4; $i++)
            <div class="mcard">
                <div class="mthumb">
                    <div class="mthumb-bg pm{{ $i }}"></div>
                    <div class="mthumb-title">
                        @if($i==1) DUNE<br>PART TWO
                        @elseif($i==2) GLADIATOR<br>II
                        @elseif($i==3) THE<br>BATMAN 2
                        @else SPIDER-MAN<br>4
                        @endif
                    </div>
                    <div class="moverlay">
                        <a class="mbtn" style="text-decoration: none; display: inline-block;" href="{{ route('booking.select', ['movie_id' => $i, 'title' => ($i==1 ? 'DUNE: PART TWO' : ($i==2 ? 'GLADIATOR II' : ($i==3 ? 'THE BATMAN 2' : 'SPIDER-MAN 4'))), 'genre' => ($i==1 ? 'Sci-Fi' : ($i==2 ? 'Action' : 'Superhero')), 'duration' => ($i==1 ? 166 : ($i==2 ? 148 : ($i==3 ? 175 : 140)))]) }}">Book Ticket</a>
                    </div>
                </div>
                <span class="genre-badge">
                    @if($i==1) Sci-Fi @elseif($i==2) Action @else Superhero @endif
                </span>
                <div class="minfo">
                    <div class="mname">
                        @if($i==1) Dune: Part Two
                        @elseif($i==2) Gladiator II
                        @elseif($i==3) The Batman 2
                        @else Spider-Man 4
                        @endif
                    </div>
                    <div class="mmeta">
                        <div class="mrating">★ 
                            @if($i==1) 8.5 @elseif($i==2) 8.3 @elseif($i==3) 8.6 @else 8.7 @endif
                            <span>({{ rand(98,315) }}K)</span>
                        </div>
                        <div class="mdur">
                            @if($i==1) 2h 46m @elseif($i==2) 2h 28m @elseif($i==3) 2h 55m @else 2h 20m @endif
                        </div>
                    </div>
                    <div class="mprice">From RM {{ $i==3 ? 20 : 18 }} <small>/ seat</small></div>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <div class="promo">
        <div class="promo-text">
            <div class="promo-ey">🎉 Limited Time Offer</div>
            <div class="promo-h">STUDENT TUESDAYS<br>— 50% OFF</div>
            <p class="promo-d">Flash your student ID every Tuesday and get half-price tickets to any screening. Valid at all CINEMAX locations across Malaysia.</p>
        </div>
        <button class="promo-btn">Claim Offer →</button>
    </div>
</div>

<style>
.hero-slider {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
    background: var(--bg);
}
.slider-container { position: relative; width: 100%; height: 100%; }
.main-slide       { position: relative; width: 100%; height: 100%; overflow: hidden; }

.slide-bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transition: background 0.6s ease;
    z-index: 1;
}
.slide-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        90deg,
        rgba(0,0,0,0.90) 0%,
        rgba(0,0,0,0.55) 45%,
        rgba(0,0,0,0.10) 100%
    );
    z-index: 2;
}

.slide-content {
    position: relative;
    z-index: 10;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    padding: 0 5% 0 12%; 
}

.movie-poster-large {
    position: relative;
    max-width: 500px;
    width: 100%;
    animation: fadeInUp 0.8s ease;
}
.poster-gradient {
    position: absolute;
    top: -20px; left: -20px; right: -20px; bottom: -20px;
    background: radial-gradient(circle, rgba(209,106,255,0.2) 0%, transparent 70%);
    filter: blur(30px);
    z-index: -1;
}

.movie-badge {
    display: inline-block;
    background: rgba(209,106,255,0.2);
    border: 1px solid var(--c1);
    color: var(--c1);
    padding: 0.25rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 1rem;
    letter-spacing: 1px;
}
.movie-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(2.5rem, 8vw, 5rem);
    line-height: 1;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--c1), var(--c2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.movie-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
    color: var(--muted);
    font-size: 0.875rem;
}
.movie-meta .rating { color: #ffc107; font-weight: 600; }
.movie-meta .dot    { color: var(--border); }

.movie-description {
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 2rem;
    font-size: 0.9375rem;
    max-width: 90%;
}
.movie-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

.btn-book {
    background: var(--grad-2);
    color: white; border: none;
    padding: 0.875rem 2rem; border-radius: 8px;
    font-weight: 600; cursor: pointer;
    transition: all 0.3s; font-size: 0.9375rem;
}
.btn-book:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(150,20,208,0.5); }

.btn-trailer {
    background: rgba(255,255,255,0.1);
    color: white; border: 1px solid var(--border);
    padding: 0.875rem 2rem; border-radius: 8px;
    font-weight: 500; cursor: pointer;
    transition: all 0.3s;
    display: flex; align-items: center; gap: 0.5rem;
}
.btn-trailer:hover { background: rgba(255,255,255,0.2); border-color: var(--c1); transform: translateY(-2px); }

.slider-nav {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    width: 60px; height: 44px;
    background: transparent; border: none; color: white;
    cursor: pointer; z-index: 20;
    transition: opacity 0.2s, transform 0.2s;
    display: flex; align-items: center; justify-content: center;
    padding: 0;
}
.slider-nav:hover { opacity: 0.45; transform: translateY(-50%) translateX(-5px); }
.prev { left: 2.5rem; }


.next-preview {
    position: absolute;
    top: 50%; right: 3rem; 
    transform: translateY(-50%);
    z-index: 20;
    display: flex; flex-direction: row; align-items: center;
    gap: 0; cursor: pointer; transition: all 0.3s ease;
}
.next-preview:hover { transform: translateY(-50%) translateX(-8px); opacity: 0.95; }

.np-left {
    display: flex; flex-direction: column;
    align-items: flex-end; gap: 5px;
    padding-right: 16px;
}
.np-text {
    font-size: 11px; letter-spacing: 2px;
    color: rgba(255,255,255,0.5);
    font-weight: 400; text-transform: lowercase;
}
.next-preview-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 16px; letter-spacing: 2px;
    color: #fff; line-height: 1.2;
    text-align: right; max-width: 130px;
}
.np-arrow-svg {
    display: block; opacity: 0.7;
    transition: opacity 0.2s, transform 0.2s;
}
.next-preview:hover .np-arrow-svg { opacity: 1; transform: translateX(4px); }

.next-preview-thumb {
    position: relative; width: 240px; height: 160px;
    overflow: hidden; flex-shrink: 0;
    border-radius: 4px;
}
.next-thumb-inner { position: absolute; inset: 0; transition: background 0.4s ease; }
.next-play-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,0.30);
    display: flex; align-items: center; justify-content: center;
}
.next-play-btn {
    width: 42px; height: 42px; border-radius: 50%;
    background: rgba(255,255,255,0.20);
    border: 2px solid rgba(255,255,255,0.65);
    display: flex; align-items: center; justify-content: center;
    transition: transform 0.2s, background 0.2s, border-color 0.2s;
}

/* ── Progress & Counter ── */
.progress-indicator {
    position: absolute; bottom: 0; left: 0; width: 100%; height: 3px;
    background: rgba(255,255,255,0.2); z-index: 20;
}
.progress-bar { height: 100%; background: var(--grad-1); width: 0%; transition: width 0.3s linear; }

.slide-counter {
    position: absolute; bottom: 2rem; right: 3rem; z-index: 20;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(10px);
    padding: 0.5rem 1rem; border-radius: 20px;
    font-size: 0.875rem; color: white; font-weight: 500; letter-spacing: 1px;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1024px) {
    .slide-content { padding-left: 15%; }
}
@media (max-width: 768px) {
    .next-preview { display: none; }
    .slide-content { padding-left: 5%; }
}
</style>

<script>
const movies = [
    {
        id: 1, title: "DUNE: PART TWO",
        rating: "8.5", duration: "2h 46m", genre: "Sci-Fi", year: "2024",
        description: "Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.",
        bgGradient: "linear-gradient(145deg, #1a0033, #660094)"
    },
    {
        id: 2, title: "THE BATMAN 2",
        rating: "8.6", duration: "2h 55m", genre: "Superhero", year: "2025",
        description: "The Dark Knight returns to face new threats in Gotham City as a mysterious villain emerges from the shadows.",
        bgGradient: "linear-gradient(145deg, #200044, #9614d0)"
    },
    {
        id: 3, title: "SPIDER-MAN 4",
        rating: "8.7", duration: "2h 20m", genre: "Superhero", year: "2026",
        description: "The web-slinger returns in his most thrilling adventure yet, facing a multiversal threat that could destroy everything.",
        bgGradient: "linear-gradient(145deg, #100020, #bb44f0)"
    },
    {
        id: 4, title: "GLADIATOR II",
        rating: "8.3", duration: "2h 28m", genre: "Action", year: "2024",
        description: "Years after witnessing the death of Maximus, Lucius is forced to enter the Colosseum after his home is conquered.",
        bgGradient: "linear-gradient(145deg, #0d0020, #4a0080)"
    },
    {
        id: 5, title: "OPPENHEIMER",
        rating: "9.0", duration: "3h 0m", genre: "Drama", year: "2023",
        description: "The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.",
        bgGradient: "linear-gradient(145deg, #1a0040, #660094)"
    },
    {
        id: 6, title: "JOHN WICK 5",
        rating: "8.1", duration: "2h 10m", genre: "Action", year: "2025",
        description: "The legendary assassin returns for one final mission that will determine the fate of the High Table.",
        bgGradient: "linear-gradient(145deg, #08000f, #9614d0)"
    }
];

let currentIndex = 0;
let autoPlayInterval, progressInterval;
const autoPlayDelay = 5000;

const slideBg = document.getElementById('slideBg');
const movieTitle = document.getElementById('movieTitle');
const movieMeta = document.getElementById('movieMeta');
const movieDesc = document.getElementById('movieDesc');
const currentSlideSpan = document.getElementById('currentSlide');
const totalSlidesSpan = document.getElementById('totalSlides');
const progressBar = document.getElementById('progressBar');
const prevBtn = document.getElementById('prevSlide');
const nextPreview = document.getElementById('nextPreview');
const nextPreviewTitle = document.getElementById('nextPreviewTitle');
const nextThumbInner = document.getElementById('nextThumbInner');
const heroBookBtn = document.getElementById('heroBookBtn');

totalSlidesSpan.textContent = String(movies.length).padStart(2, '0');

function updateNextPreview() {
    const next = movies[(currentIndex + 1) % movies.length];
    nextPreviewTitle.textContent = next.title;
    nextThumbInner.style.background = next.bgGradient;
}

function updateSlide() {
    const movie = movies[currentIndex];
    slideBg.style.background = movie.bgGradient;

    const poster = document.querySelector('.movie-poster-large');
    poster.style.animation = 'none';
    poster.offsetHeight;
    poster.style.animation = 'fadeInUp 0.5s ease';

    movieTitle.textContent = movie.title;
    movieMeta.innerHTML = `
        <span class="rating">★ ${movie.rating}</span>
        <span class="dot">•</span>
        <span>${movie.duration}</span>
        <span class="dot">•</span>
        <span>${movie.genre}</span>
        <span class="dot">•</span>
        <span>${movie.year}</span>
    `;
    movieDesc.textContent = movie.description;
    currentSlideSpan.textContent = String(currentIndex + 1).padStart(2, '0');

    if (heroBookBtn) {
        const params = new URLSearchParams({
            movie_id: movie.id,
            title: movie.title,
            genre: movie.genre,
            duration: String(parseInt(movie.duration, 10) * 60 + parseInt(movie.duration.split('h ')[1], 10) || 120),
        });
        heroBookBtn.href = `{{ url('/booking/select') }}?${params.toString()}`;
    }

    updateNextPreview();
    resetProgressBar();
}

function nextSlide() { currentIndex = (currentIndex + 1) % movies.length; updateSlide(); resetAutoPlay(); }
function prevSlide() { currentIndex = (currentIndex - 1 + movies.length) % movies.length; updateSlide(); resetAutoPlay(); }

nextPreview.addEventListener('click', nextSlide);

function startProgressBar() {
    if (progressInterval) clearInterval(progressInterval);
    progressBar.style.width = '0%';
    let w = 0;
    progressInterval = setInterval(() => {
        if (w >= 100) clearInterval(progressInterval);
        else progressBar.style.width = (++w) + '%';
    }, autoPlayDelay / 100);
}

function resetProgressBar() { clearInterval(progressInterval); startProgressBar(); }

function startAutoPlay() {
    if (autoPlayInterval) clearInterval(autoPlayInterval);
    autoPlayInterval = setInterval(nextSlide, autoPlayDelay);
    startProgressBar();
}

function resetAutoPlay() { clearInterval(autoPlayInterval); startAutoPlay(); }
function stopAutoPlay() { clearInterval(autoPlayInterval); autoPlayInterval = null; clearInterval(progressInterval); }

prevBtn.addEventListener('click', () => { prevSlide(); resetAutoPlay(); });

const slider = document.querySelector('.hero-slider');
slider.addEventListener('mouseenter', stopAutoPlay);
slider.addEventListener('mouseleave', startAutoPlay);

updateSlide();
startAutoPlay();
</script>
@endsection

