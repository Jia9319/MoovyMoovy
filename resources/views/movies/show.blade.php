@extends('layouts.app')

@section('title', $movie->title . ' - MoovyMoovy')

@section('content')


<div class="movie-hero-premium">
    <div class="movie-hero-container">
        <div class="movie-poster-card">
            @if($movie->poster)
                <div class="poster-wrapper">
                    <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}">
                    <div class="poster-badge">
                        <i class="fas fa-play"></i>
                        <span>Now Showing</span>
                    </div>
                </div>
            @else
                <div class="poster-placeholder-premium">
                    <i class="fas fa-film"></i>
                    <span>{{ strtoupper($movie->title) }}</span>
                </div>
            @endif
        </div>

        <div class="movie-info-premium">
            <div class="info-header">
                <div class="movie-rating-badge">
                    <i class="fas fa-star"></i>
                    <span>{{ $avgRating ?: '0.0' }}</span>
                </div>
                <div class="movie-review-count">
                    <i class="fas fa-comments"></i>
                    <span>{{ $reviewCount }} Reviews</span>
                </div>
            </div>

            <h1 class="movie-title-premium">{{ $movie->title }}</h1>
            
            <div class="movie-meta-grid">
                <div class="meta-item-premium">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span class="meta-label">Duration</span>
                        <strong>{{ $movie->duration }} min</strong>
                    </div>
                </div>
                <div class="meta-item-premium">
                    <i class="fas fa-tag"></i>
                    <div>
                        <span class="meta-label">Genre</span>
                        <strong>{{ $movie->genre }}</strong>
                    </div>
                </div>
                <div class="meta-item-premium">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <span class="meta-label">Release Year</span>
                        <strong>{{ $movie->release_date->format('Y') }}</strong>
                    </div>
                </div>
            </div>

            <div class="movie-description-premium">
                <h3>Synopsis</h3>
                <p>{{ $movie->description }}</p>
            </div>

            <div class="movie-actions-premium">
                <button class="btn-book-premium" id="bookNowBtn">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Book Tickets</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button class="btn-trailer-premium">
                    <i class="fas fa-play"></i>
                    <span>Watch Trailer</span>
                </button>

                @auth
                <div class="admin-actions">
                    <a href="{{ route('movies.edit', $movie->id) }}" class="btn-edit-premium">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('movies.destroy', $movie->id) }}" method="POST"
                          onsubmit="return confirm('Delete this movie and all its data?')"
                          style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete-premium">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </div>
</div>


<section class="showtimes-premium">
    <div class="section-header-premium">
        <div class="header-left">
            <h2 class="section-title-premium">
                <i class="fas fa-clock"></i>
                Showtimes
            </h2>
            <p class="section-subtitle-premium">Select your preferred time and cinema</p>
        </div>
        @auth
        <a href="{{ route('showtimes.create') }}?movie_id={{ $movie->id }}" class="btn-add-premium">
            <i class="fas fa-plus"></i>
            <span>Add Showtime</span>
        </a>
        @endauth
    </div>

    @forelse($movie->showtimes as $showtime)
    <div class="showtime-card-premium">
        <div class="showtime-left">
            <div class="cinema-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="showtime-info">
                <h4 class="cinema-name">{{ $showtime->cinema }}</h4>
                <div class="showtime-details">
                    <span class="showtime-date">
                        <i class="far fa-calendar-alt"></i>
                        {{ $showtime->date->format('l, d M Y') }}
                    </span>
                    <span class="showtime-time">
                        <i class="far fa-clock"></i>
                        {{ date('h:i A', strtotime($showtime->time)) }}
                    </span>
                    @if($showtime->hall)
                    <span class="showtime-hall">
                        <i class="fas fa-door-open"></i>
                        Hall {{ $showtime->hall }}
                    </span>
                    @endif
                    @if($showtime->format)
                    <span class="showtime-format">{{ $showtime->format }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="showtime-right">
            <div class="showtime-price">
                <span class="price-label">From</span>
                <strong>RM {{ number_format($showtime->price, 2) }}</strong>
            </div>
            
            <a href="{{ route('booking.seat', ['movie_id' => $movie->id, 'showtime_id' => $showtime->id]) }}" class="btn-select-seat">
                Select Seats
                <i class="fas fa-arrow-right"></i>
            </a>

            @auth
            <div class="showtime-admin">
                <a href="{{ route('showtimes.edit', $showtime->id) }}" class="admin-icon edit">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('showtimes.destroy', $showtime->id) }}" method="POST"
                      onsubmit="return confirm('Delete this showtime?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-icon delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>
    @empty
    <div class="empty-state-premium">
        <i class="fas fa-calendar-times"></i>
        <h3>No Showtimes Available</h3>
        <p>Check back later for showtimes.</p>
        @auth
        <a href="{{ route('showtimes.create') }}?movie_id={{ $movie->id }}" class="btn-primary">Add Showtime</a>
        @endauth
    </div>
    @endforelse
</section>

<section class="reviews-premium">
    <div class="section-header-premium">
        <div class="header-left">
            <h2 class="section-title-premium">
                <i class="fas fa-star"></i>
                Audience Reviews
            </h2>
            <p class="section-subtitle-premium">What people are saying about this movie</p>
        </div>
        <div class="header-right">
            <select id="sortReviews" class="sort-select-premium">
                <option value="latest">Latest Reviews</option>
                <option value="highest">Highest Rated</option>
                <option value="lowest">Lowest Rated</option>
            </select>
            @auth
            <button id="openReviewModal" class="btn-write-review">
                <i class="fas fa-pen"></i>
                Write a Review
            </button>
            @else
            <a href="{{ route('login') }}" class="btn-write-review">
                <i class="fas fa-sign-in-alt"></i>
                Login to Review
            </a>
            @endauth
        </div>
    </div>

    @if($reviewCount > 0)
    <div class="rating-summary-premium">
        <div class="rating-score">
            <div class="score-number">{{ number_format($avgRating, 1) }}</div>
            <div class="score-stars">
                @for($i=1;$i<=5;$i++)
                    <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i>
                @endfor
            </div>
            <div class="score-total">{{ $reviewCount }} reviews</div>
        </div>
        <div class="rating-bars">
            @for($i=5;$i>=1;$i--)
            <div class="rating-bar-item">
                <div class="rating-bar-label">{{ $i }} stars</div>
                <div class="rating-bar-track">
                    <div class="rating-bar-fill" style="width: {{ $ratingBreakdown[$i] ?? 0 }}%;"></div>
                </div>
                <div class="rating-bar-percent">{{ $ratingBreakdown[$i] ?? 0 }}%</div>
            </div>
            @endfor
        </div>
    </div>
    @endif

    <div id="reviewsList" class="reviews-grid">
        @forelse($movie->reviews->sortByDesc('created_at') as $review)
        <div class="review-card-premium"
             data-rating="{{ $review->rating }}"
             data-date="{{ $review->created_at->timestamp }}">
            <div class="review-header-premium">
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        @if($review->is_anonymous)
                            <i class="fas fa-user-secret"></i>
                        @else
                            <i class="fas fa-user"></i>
                        @endif
                    </div>
                    <div>
                        <div class="reviewer-name">
                            {{ $review->is_anonymous ? 'Anonymous User' : $review->user->name }}
                        </div>
                        <div class="review-rating">
                            @for($i=1;$i<=5;$i++)
                                <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="review-date">
                    <i class="far fa-calendar-alt"></i>
                    {{ $review->created_at->diffForHumans() }}
                </div>
            </div>
            
            @if($review->title)
            <h4 class="review-title">{{ $review->title }}</h4>
            @endif
            
            <p class="review-content">{{ $review->content }}</p>
            
            <div class="review-footer">
                <button class="like-btn-premium" data-id="{{ $review->id }}">
                    <i class="far fa-thumbs-up"></i>
                    <span class="like-count">{{ $review->likes->count() }}</span>
                </button>
                <button class="share-btn-premium">
                    <i class="fas fa-share-alt"></i>
                    <span>Share</span>
                </button>
                @auth
                @if(auth()->id() == $review->user_id)
                <div class="review-actions">
                    <button class="edit-review-btn"
                        data-id="{{ $review->id }}"
                        data-rating="{{ $review->rating }}"
                        data-title="{{ $review->title }}"
                        data-content="{{ $review->content }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
                          onsubmit="return confirm('Delete your review?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="delete-review-btn">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
        </div>
        @empty
        <div class="empty-state-premium">
            <i class="fas fa-comment-slash"></i>
            <h3>No Reviews Yet</h3>
            <p>Be the first to share your thoughts about this movie!</p>
            @auth
            <button id="openReviewModalEmpty" class="btn-write-review">
                Write a Review
            </button>
            @endauth
        </div>
        @endforelse
    </div>
</section>


<div id="reviewModal" class="modal-premium">
    <div class="modal-content-premium">
        <div class="modal-header-premium">
            <h3>
                <i class="fas fa-star" style="color: #ffc107;"></i>
                Write Your Review
            </h3>
            <button class="modal-close-premium">&times;</button>
        </div>
        @auth
        <form id="reviewForm" action="{{ route('reviews.store', $movie->id) }}" method="POST">
            @csrf
            <div class="form-group-premium">
                <label class="form-label-premium">Your Rating <span class="required">*</span></label>
                <div class="star-rating-select">
                    @for($i=1;$i<=5;$i++)
                    <i class="far fa-star star-pick" data-val="{{ $i }}"></i>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="">
                <div id="ratingErr" class="field-error">Please select a rating</div>
            </div>
            
            <div class="form-group-premium">
                <label class="form-label-premium">Review Title</label>
                <input type="text" name="title" class="form-input-premium" placeholder="Summarize your experience">
            </div>
            
            <div class="form-group-premium">
                <label class="form-label-premium">Your Review <span class="required">*</span></label>
                <textarea name="content" class="form-textarea-premium" rows="5" 
                    placeholder="What did you think about this movie?"></textarea>
                <div id="contentErr" class="field-error">Please write at least 10 characters</div>
            </div>
            
            <label class="checkbox-premium">
                <input type="checkbox" name="is_anonymous" value="1">
                <span>Post anonymously</span>
            </label>
            
            <div class="modal-buttons">
                <button type="submit" class="btn-submit-premium">
                    <i class="fas fa-paper-plane"></i> Submit Review
                </button>
                <button type="button" class="btn-cancel-premium">Cancel</button>
            </div>
        </form>
        @endauth
    </div>
</div>

<div id="editReviewModal" class="modal-premium">
    <div class="modal-content-premium">
        <div class="modal-header-premium">
            <h3>
                <i class="fas fa-edit"></i>
                Edit Your Review
            </h3>
            <button class="modal-close-premium">&times;</button>
        </div>
        <form id="editReviewForm" method="POST">
            @csrf @method('PUT')
            <div class="form-group-premium">
                <label class="form-label-premium">Rating</label>
                <div class="star-rating-select">
                    @for($i=1;$i<=5;$i++)
                    <i class="far fa-star edit-star" data-val="{{ $i }}"></i>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="editRatingInput" value="">
            </div>
            
            <div class="form-group-premium">
                <label class="form-label-premium">Review Title</label>
                <input type="text" name="title" id="editTitle" class="form-input-premium">
            </div>
            
            <div class="form-group-premium">
                <label class="form-label-premium">Your Review</label>
                <textarea name="content" id="editContent" class="form-textarea-premium" rows="5"></textarea>
            </div>
            
            <div class="modal-buttons">
                <button type="submit" class="btn-submit-premium">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button type="button" class="btn-cancel-premium">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal helpers
    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Star rating system
    function highlightStars(stars, value) {
        stars.forEach(star => {
            const starVal = parseInt(star.dataset.val);
            if (starVal <= value) {
                star.classList.add('fas');
                star.classList.remove('far');
            } else {
                star.classList.add('far');
                star.classList.remove('fas');
            }
        });
    }

    function initStars(selector, inputId) {
        const stars = document.querySelectorAll(selector);
        const input = document.getElementById(inputId);
        if (!stars.length || !input) return;
        
        stars.forEach(star => {
            star.addEventListener('mouseenter', () => {
                highlightStars(stars, parseInt(star.dataset.val));
            });
            star.addEventListener('mouseleave', () => {
                highlightStars(stars, parseInt(input.value) || 0);
            });
            star.addEventListener('click', () => {
                input.value = star.dataset.val;
                highlightStars(stars, parseInt(star.dataset.val));
            });
        });
    }

    initStars('.star-pick', 'ratingInput');
    initStars('.edit-star', 'editRatingInput');

    // Write Review Modal
    const writeModal = document.getElementById('reviewModal');
    const openBtns = document.querySelectorAll('#openReviewModal, #openReviewModalEmpty');
    openBtns.forEach(btn => {
        if (btn) btn.addEventListener('click', () => openModal(writeModal));
    });

    if (writeModal) {
        writeModal.addEventListener('click', (e) => {
            if (e.target === writeModal) closeModal(writeModal);
        });
    }

    // Close modal buttons
    document.querySelectorAll('.modal-close-premium, .btn-cancel-premium').forEach(btn => {
        btn.addEventListener('click', () => {
            closeModal(writeModal);
            closeModal(document.getElementById('editReviewModal'));
        });
    });

    // Form validation
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', (e) => {
            const rating = document.getElementById('ratingInput')?.value;
            const content = reviewForm.querySelector('[name=content]')?.value.trim();
            const ratingErr = document.getElementById('ratingErr');
            const contentErr = document.getElementById('contentErr');
            
            let hasError = false;
            if (!rating) {
                if (ratingErr) ratingErr.style.display = 'block';
                hasError = true;
            } else if (ratingErr) ratingErr.style.display = 'none';
            
            if (!content || content.length < 10) {
                if (contentErr) contentErr.style.display = 'block';
                hasError = true;
            } else if (contentErr) contentErr.style.display = 'none';
            
            if (hasError) e.preventDefault();
        });
    }

    // Edit Review Modal
    const editModal = document.getElementById('editReviewModal');
    document.querySelectorAll('.edit-review-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const form = document.getElementById('editReviewForm');
            if (form) form.action = '/reviews/' + btn.dataset.id;
            
            const editTitle = document.getElementById('editTitle');
            const editContent = document.getElementById('editContent');
            const editRatingInput = document.getElementById('editRatingInput');
            
            if (editTitle) editTitle.value = btn.dataset.title || '';
            if (editContent) editContent.value = btn.dataset.content || '';
            if (editRatingInput) editRatingInput.value = btn.dataset.rating;
            
            highlightStars(document.querySelectorAll('.edit-star'), parseInt(btn.dataset.rating));
            openModal(editModal);
        });
    });

    document.querySelectorAll('.like-btn-premium').forEach(btn => {
        btn.addEventListener('click', async () => {
            try {
                const response = await fetch('/reviews/' + btn.dataset.id + '/like', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                const likeCount = btn.querySelector('.like-count');
                const icon = btn.querySelector('i');
                if (likeCount) likeCount.textContent = data.like_count;
                if (icon) icon.className = data.liked ? 'fas fa-thumbs-up' : 'far fa-thumbs-up';
            } catch (error) {
                console.error('Error liking review:', error);
            }
        });
    });

    const sortSelect = document.getElementById('sortReviews');
    if (sortSelect) {
        sortSelect.addEventListener('change', () => {
            const cards = Array.from(document.querySelectorAll('.review-card-premium'));
            const list = document.getElementById('reviewsList');
            const value = sortSelect.value;
            
            cards.sort((a, b) => {
                if (value === 'highest') return b.dataset.rating - a.dataset.rating;
                if (value === 'lowest') return a.dataset.rating - b.dataset.rating;
                return b.dataset.date - a.dataset.date;
            });
            
            cards.forEach(card => list.appendChild(card));
        });
    }

    const bookBtn = document.getElementById('bookNowBtn');
    if (bookBtn) {
        bookBtn.addEventListener('click', () => {
            const firstShowtime = document.querySelector('.btn-select-seat');
            if (firstShowtime) {
                firstShowtime.scrollIntoView({ behavior: 'smooth' });
                firstShowtime.classList.add('pulse-animation');
                setTimeout(() => firstShowtime.classList.remove('pulse-animation'), 1000);
            } else {
                alert('No showtimes available. Please check back later!');
            }
        });
    }

    const trailerBtn = document.querySelector('.btn-trailer-premium');
    if (trailerBtn) {
        trailerBtn.addEventListener('click', () => {
            alert('🎬 Trailer coming soon!');
        });
    }
});
</script>
@endpush
@endsection