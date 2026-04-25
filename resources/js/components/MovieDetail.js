import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';

axios.defaults.withCredentials = true;

class MovieDetail extends Component {
    constructor(props) {
        super(props);

        const userId = window.Laravel?.userId || null;
        const isAdmin = window.Laravel?.isAdmin || false;

        this.state = {
            movie: null,
            showtimes: [],
            reviews: [],
            cinemas: [],
            loading: true,
            
            editMovieModal: false,
            editMovieForm: {
                title: '',
                description: '',
                genre: '',
                duration: '',
                release_date: '',
                rating: '',
                status: 'now_showing',
                expected_release: '',
                poster: null
            },
            
            showtimeModal: false,
            editingShowtime: null,
            showtimeForm: {
                cinema_id: '',
                hall: '',
                format: '',
                date: '',
                time: '',
                price: ''
            },
            
            reviewModal: false,
            editingReview: null,
            reviewRating: 0,
            reviewHoverRating: 0,
            reviewTitle: '',
            reviewContent: '',
            reviewIsAnonymous: false,
            
            currentUserId: userId,
            isAdmin: isAdmin,
        };
        
        this.addShowtime = this.addShowtime.bind(this);
        this.updateShowtime = this.updateShowtime.bind(this);
        this.deleteShowtime = this.deleteShowtime.bind(this);
        this.openShowtimeModal = this.openShowtimeModal.bind(this);
        this.closeShowtimeModal = this.closeShowtimeModal.bind(this);
        this.handleShowtimeChange = this.handleShowtimeChange.bind(this);
    }

    componentDidMount() {
        this.loadMovieData();
        this.loadCinemas();
    }

    loadMovieData = async () => {
        try {
            const mountEl = document.getElementById('movieDetail');
            const movieId = mountEl?.dataset.movieId;

            if (!movieId) {
                console.error('Movie ID not found');
                this.setState({ loading: false });
                return;
            }

            const res = await axios.get(`/api/movies/${movieId}`);
            const reviews = res.data.reviews || [];

            let avgRating = 0;
            if (reviews.length > 0) {
                const sum = reviews.reduce((t, r) => t + parseFloat(r.rating), 0);
                avgRating = parseFloat((sum / reviews.length).toFixed(1));
            }

            this.setState({
                movie: {
                    ...res.data.movie,
                    avgRating,
                    reviewCount: reviews.length,
                },
                showtimes: res.data.showtimes || [],
                reviews,
                loading: false,
            });
        } catch (err) {
            console.error('loadMovieData error:', err.response?.data || err.message);
            this.setState({ loading: false });
        }
    };

    loadCinemas = async () => {
        try {
            const response = await axios.get('/api/cinemas/list');
            this.setState({ cinemas: response.data });
        } catch (error) {
            console.error('Failed to load cinemas:', error);
        }
    };

    openEditMovieModal = () => {
        const movie = this.state.movie;
        
        this.setState({
            editMovieModal: true,
            editMovieForm: {
                title: movie?.title || '',
                description: movie?.description || '',
                genre: movie?.genre || '',
                duration: movie?.duration || '',
                release_date: movie?.release_date ? movie.release_date.split('T')[0] : '',
                rating: movie?.rating || '',
                status: movie?.status || 'now_showing',
                expected_release: movie?.expected_release ? movie.expected_release.split('T')[0] : '',
                poster: null
            }
        });
    };

    closeEditMovieModal = () => {
        this.setState({ editMovieModal: false });
    };

    handleEditMovieChange = (e) => {
        const { name, value, type, files } = e.target;
        this.setState({
            editMovieForm: {
                ...this.state.editMovieForm,
                [name]: type === 'file' ? files[0] : value
            }
        });
    };

    updateMovie = async () => {
        if (!this.state.movie?.id) {
            alert('Movie ID not found');
            return;
        }
        
        if (!this.state.editMovieForm.title) {
            alert('Title is required');
            return;
        }
        if (!this.state.editMovieForm.description) {
            alert('Description is required');
            return;
        }
        if (!this.state.editMovieForm.genre) {
            alert('Genre is required');
            return;
        }
        if (!this.state.editMovieForm.duration) {
            alert('Duration is required');
            return;
        }
        if (!this.state.editMovieForm.release_date) {
            alert('Release date is required');
            return;
        }
        if (!this.state.editMovieForm.status) {
            alert('Status is required');
            return;
        }
        
        try {
            const formData = new FormData();
            
            Object.keys(this.state.editMovieForm).forEach(key => {
                if (key === 'poster') {
                    if (this.state.editMovieForm.poster) {
                        formData.append('poster', this.state.editMovieForm.poster);
                    }
                } else if (this.state.editMovieForm[key] !== null && this.state.editMovieForm[key] !== '') {
                    formData.append(key, this.state.editMovieForm[key]);
                }
            });
            
            formData.append('_method', 'PUT');
            
            const response = await axios.post(`/api/movies/${this.state.movie.id}`, formData, {
                headers: { 
                    'Content-Type': 'multipart/form-data'
                }
            });
            
            if (response.data.success || response.data.message === 'Movie updated successfully!') {
                this.closeEditMovieModal();
                await this.loadMovieData();
                alert('Movie updated successfully!');
            } else {
                alert(response.data.message || 'Failed to update movie');
            }
        } catch (err) {
            console.error('Update movie error:', err);
            
            if (err.response?.data?.errors) {
                const errors = err.response.data.errors;
                let errorMsg = 'Validation failed:\n';
                for (const [field, messages] of Object.entries(errors)) {
                    errorMsg += `${field}: ${messages.join(', ')}\n`;
                }
                alert(errorMsg);
            } else if (err.response?.data?.message) {
                alert(err.response.data.message);
            } else {
                alert('Failed to update movie. Check console for details.');
            }
        }
    };

    deleteMovie = async () => {
        if (!window.confirm('Delete this movie? All showtimes and reviews will also be deleted.')) return;
        try {
            await axios.delete(`/api/movies/${this.state.movie.id}`);
            alert('Movie deleted successfully!');
            window.location.href = '/movies';
        } catch (err) {
            console.error('deleteMovie error:', err.response?.data);
            alert(err.response?.data?.message || 'Failed to delete movie');
        }
    };

    openShowtimeModal = () => {
        this.setState({
            showtimeModal: true,
            editingShowtime: null,
            showtimeForm: { cinema_id: '', hall: '', format: '', date: '', time: '', price: '' },
        });
    };

    closeShowtimeModal = () => {
        this.setState({ showtimeModal: false, editingShowtime: null });
    };

    handleShowtimeChange = (e) => {
        this.setState({
            showtimeForm: { ...this.state.showtimeForm, [e.target.name]: e.target.value },
        });
    };

    addShowtime = async () => {
        const { cinema_id, date, time, price } = this.state.showtimeForm;
        
        if (!cinema_id) { alert('Please select a cinema'); return; }
        if (!date) { alert('Please select a date'); return; }
        if (!time) { alert('Please select a time'); return; }

        try {
            const movieId = this.state.movie.id;
            const response = await axios.post(`/api/movies/${movieId}/showtimes`, this.state.showtimeForm);
            
            if (response.data.success) {
                this.closeShowtimeModal();
                this.loadMovieData();
                alert('Showtime added successfully!');
            } else {
                alert(response.data.message || 'Failed to add showtime');
            }
        } catch (err) {
            console.error('Add showtime error:', err.response?.data);
            alert(err.response?.data?.message || 'Failed to add showtime');
        }
    };

    editShowtime = (st) => {
        this.setState({
            editingShowtime: st,
            showtimeForm: {
                cinema_id: st.cinema_id || '',
                hall: st.hall || '',
                format: st.format || '',
                date: st.date || '',
                time: st.time || '',
            },
            showtimeModal: true,
        });
    };

    updateShowtime = async () => {
        if (!this.state.editingShowtime) {
            alert('No showtime selected for editing');
            return;
        }
        
        const { cinema_id, date, time, price } = this.state.showtimeForm;
        if (!cinema_id) { alert('Please select a cinema'); return; }
        if (!date) { alert('Please select a date'); return; }
        if (!time) { alert('Please select a time'); return; }

        try {
            const id = this.state.editingShowtime.id;
            const response = await axios.put(`/api/showtimes/${id}`, this.state.showtimeForm);
            
            if (response.data.success) {
                this.closeShowtimeModal();
                this.loadMovieData();
                alert('Showtime updated successfully!');
            } else {
                alert(response.data.message || 'Failed to update showtime');
            }
        } catch (err) {
            console.error('Update error:', err);
            
            if (err.response?.data?.errors) {
                const errors = err.response.data.errors;
                let errorMsg = 'Validation failed:\n';
                for (const [field, messages] of Object.entries(errors)) {
                    errorMsg += `${field}: ${messages.join(', ')}\n`;
                }
                alert(errorMsg);
            } else if (err.response?.data?.message) {
                alert(err.response.data.message);
            } else {
                alert('Failed to update showtime. Check console for details.');
            }
        }
    };

    deleteShowtime = async (id) => {
        if (!window.confirm('Delete this showtime?')) return;
        try {
            await axios.delete(`/api/showtimes/${id}`);
            alert('Showtime deleted successfully!');
            this.loadMovieData();
        } catch (err) {
            console.error('Delete showtime error:', err.response?.data);
            alert(err.response?.data?.message || 'Failed to delete showtime');
        }
    };

    openReviewModal = () => {
        this.setState({
            reviewModal: true,
            editingReview: null,
            reviewRating: 0,
            reviewTitle: '',
            reviewContent: '',
            reviewIsAnonymous: false,
            reviewHoverRating: 0
        });
    };

    closeReviewModal = () => {
        this.setState({ 
            reviewModal: false, 
            editingReview: null,
            reviewRating: 0,
            reviewTitle: '',
            reviewContent: '',
            reviewIsAnonymous: false,
            reviewHoverRating: 0
        });
    };
    
    handleStarClick = (r) => this.setState({ reviewRating: r });
    handleStarHover = (r) => this.setState({ reviewHoverRating: r });
    handleStarLeave = () => this.setState({ reviewHoverRating: 0 });

    submitReview = async (e) => {
        e.preventDefault();
        
        if (this.state.reviewRating === 0) {
            alert('Please select a rating');
            return;
        }

        if (!this.state.reviewContent.trim()) {
            alert('Please write a review');
            return;
        }

        const data = {
            rating: this.state.reviewRating,
            title: this.state.reviewTitle,
            content: this.state.reviewContent,
            is_anonymous: this.state.reviewIsAnonymous,
        };

        try {
            let response;
            if (this.state.editingReview) {
                response = await axios.put(`/api/reviews/${this.state.editingReview.id}`, data);
                alert('Review updated!');
            } else {
                response = await axios.post(`/api/movies/${this.state.movie.id}/reviews`, data);
                alert('Review submitted!');
            }
            
            if (response.data.success) {
                this.closeReviewModal();
                this.setState({
                    reviewRating: 0,
                    reviewTitle: '',
                    reviewContent: '',
                    reviewIsAnonymous: false,
                    editingReview: null
                });
                await this.loadMovieData();
            } else {
                alert(response.data.message || 'Failed to save review');
            }
        } catch (err) {
            console.error('submitReview error:', err);
            
            if (err.response?.data?.errors) {
                const errors = err.response.data.errors;
                let errorMsg = 'Validation failed:\n';
                for (const [field, messages] of Object.entries(errors)) {
                    errorMsg += `${field}: ${messages.join(', ')}\n`;
                }
                alert(errorMsg);
            } else if (err.response?.data?.message) {
                alert(err.response.data.message);
            } else {
                alert('Failed to save review. Please try again.');
            }
        }
    };

    editReview = (rv) => {
        this.setState({
            editingReview: rv,
            reviewModal: true,
            reviewRating: rv.rating,
            reviewTitle: rv.title || '',
            reviewContent: rv.content || '',
            reviewIsAnonymous: rv.is_anonymous || false,
        });
    };

    deleteReview = async (id) => {
        if (!window.confirm('Delete this review?')) return;
        try {
            await axios.delete(`/api/reviews/${id}`);
            this.loadMovieData();
            alert('Review deleted!');
        } catch (err) {
            console.error('deleteReview error:', err.response?.data);
            alert(err.response?.data?.message || 'Failed to delete review');
        }
    };

    renderStars = () => {
        const active = this.state.reviewHoverRating || this.state.reviewRating;
        return [1, 2, 3, 4, 5].map(i => (
            <i key={i}
                className={i <= active ? 'fas fa-star' : 'far fa-star'}
                style={{ fontSize: '2rem', cursor: 'pointer', color: '#ffc107', marginRight: '4px' }}
                onClick={() => this.handleStarClick(i)}
                onMouseEnter={() => this.handleStarHover(i)}
                onMouseLeave={this.handleStarLeave}
            />
        ));
    };

    render() {
        const {
            movie, showtimes, reviews, cinemas, loading,
            editMovieModal, editMovieForm,
            showtimeModal, editingShowtime, showtimeForm,
            reviewModal, reviewTitle, reviewContent, reviewIsAnonymous,
            currentUserId, isAdmin,
        } = this.state;

        if (loading) {
            return (
                <div className="movie-detail-loading">
                    Loading...
                </div>
            );
        }

        if (!movie) {
            return <div className="movie-detail-not-found">Movie not found</div>;
        }

        const heroBgStyle = movie.poster
            ? { background: `linear-gradient(105deg, rgba(0,0,0,0.9) 35%, rgba(0,0,0,0.3) 100%), url('/storage/${movie.poster}') center/cover no-repeat` }
            : { background: 'linear-gradient(135deg, #1a0033, #0a0018)' };

        return (
            <div>
                <div className="movie-detail-hero-section" style={heroBgStyle}>
                    <div className="movie-detail-hero-content">
                        <div className="movie-detail-hero-max-width">
                            <h1 className="movie-detail-hero-title">{movie.title}</h1>
                            <div className="movie-detail-hero-details">
                                <span>★ {movie.avgRating || movie.rating || '0.0'}</span>
                                <span>{movie.duration} min</span>
                                <span>{movie.genre}</span>
                                <span>{new Date(movie.release_date).getFullYear()}</span>
                                {movie.reviewCount > 0 && <span>• {movie.reviewCount} reviews</span>}
                            </div>
                            <p className="movie-detail-hero-description">{movie.description}</p>
                            <div className="movie-detail-hero-buttons">
                                <button 
                                    onClick={() => window.location.href = `/booking/select?movie_id=${movie.id}`}
                                    className="movie-detail-btn-book"
                                >
                                    Book Tickets
                                </button>
                                {isAdmin && (
                                    <>
                                        <button onClick={this.openEditMovieModal} className="movie-detail-btn-edit">
                                            Edit Movie
                                        </button>
                                        <button onClick={this.deleteMovie} className="movie-detail-btn-delete">
                                            Delete Movie
                                        </button>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </div>

                <section className="movie-detail-showtimes-section">
                    <div className="movie-detail-showtimes-header">
                        <h2 className="movie-detail-showtimes-title">Showtimes</h2>
                    </div>

                    {showtimes.length === 0 ? (
                        <div className="movie-detail-showtimes-empty">
                            No showtimes available yet.
                            {isAdmin && (
                                <div className="movie-detail-showtimes-empty-btn">
                                    <button onClick={this.openShowtimeModal} className="movie-detail-btn-add-showtime">
                                        + Add First Showtime
                                    </button>
                                </div>
                            )}
                        </div>
                    ) : (
                        showtimes.map(st => (
                            <div key={st.id} className="movie-detail-showtime-item">
                                <div className="movie-detail-showtime-info">
                                    <div className="movie-detail-showtime-cinema">
                                        {st.cinema?.name || 'Unknown Cinema'} {st.hall ? `— Hall ${st.hall}` : ''} {st.format ? `(${st.format})` : ''}
                                    </div>
                                    <div className="movie-detail-showtime-datetime">
                                        {new Date(st.date).toLocaleDateString('en-MY', { 
                                            weekday: 'long', 
                                            year: 'numeric', 
                                            month: 'long', 
                                            day: 'numeric' 
                                        })} at {st.time}                                    
                                    </div>
                                </div>
                                <div className="movie-detail-showtime-actions">
                                    {isAdmin && (
                                        <>
                                            <button onClick={() => this.editShowtime(st)} className="movie-detail-btn-edit-showtime">
                                                <i className="fas fa-edit"></i>
                                            </button>
                                            <button onClick={() => this.deleteShowtime(st.id)} className="movie-detail-btn-delete-showtime">
                                                <i className="fas fa-trash"></i>
                                            </button>
                                        </>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </section>

                <section className="movie-detail-reviews-section">
                    <div className="movie-detail-reviews-header">
                        <h2 className="movie-detail-reviews-title">Reviews</h2>
                        <button onClick={this.openReviewModal} className="movie-detail-btn-write-review">
                            ✏ Write a Review
                        </button>
                    </div>

                    {reviews.length === 0 ? (
                        <div className="movie-detail-reviews-empty">
                            No reviews yet. Be the first to review!
                        </div>
                    ) : (
                        reviews.map(rv => {
                            const isOwner = currentUserId && rv.user_id === parseInt(currentUserId);
                            const canEdit = isOwner;
                            const canDelete = isOwner || isAdmin;
                            return (
                                <div key={rv.id} className="movie-detail-review-item">
                                    <div className="movie-detail-review-header">
                                        <div className="movie-detail-review-user">
                                            <div className="movie-detail-review-avatar">
                                                <i className="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <div className="movie-detail-review-user-name">
                                                    {rv.is_anonymous ? 'Anonymous' : rv.user?.name}
                                                </div>
                                                <div className="movie-detail-review-stars">
                                                    {[1, 2, 3, 4, 5].map(s => (
                                                        <i key={s} className={s <= rv.rating ? 'fas fa-star' : 'far fa-star'}></i>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="movie-detail-review-date">
                                            {new Date(rv.created_at).toLocaleDateString()}
                                        </div>
                                    </div>
                                    {rv.title && <h5 className="movie-detail-review-title">{rv.title}</h5>}
                                    <p className="movie-detail-review-content">{rv.content}</p>
                                    {(canEdit || canDelete) && (
                                        <div className="movie-detail-review-actions">
                                            {canEdit && (
                                                <button onClick={() => this.editReview(rv)} className="movie-detail-btn-edit-review">
                                                    <i className="fas fa-edit"></i> Edit
                                                </button>
                                            )}
                                            {canDelete && (
                                                <button onClick={() => this.deleteReview(rv.id)} className="movie-detail-btn-delete-review">
                                                    <i className="fas fa-trash"></i> Delete
                                                </button>
                                            )}
                                        </div>
                                    )}
                                </div>
                            );
                        })
                    )}
                </section>

                {editMovieModal && (
                    <div className="movie-detail-modal-overlay">
                        <div className="movie-detail-modal-container">
                            <div className="movie-detail-modal-header">
                                <h3 className="movie-detail-modal-title">Edit Movie</h3>
                                <button onClick={this.closeEditMovieModal} className="movie-detail-modal-close">&times;</button>
                            </div>
                            
                            <input type="text" name="title" placeholder="Title" value={editMovieForm.title} onChange={this.handleEditMovieChange} className="movie-detail-input" />
                            <textarea name="description" placeholder="Description" value={editMovieForm.description} onChange={this.handleEditMovieChange} rows="4" className="movie-detail-textarea" />
                            <input type="text" name="genre" placeholder="Genre" value={editMovieForm.genre} onChange={this.handleEditMovieChange} className="movie-detail-input" />
                            <input type="number" name="duration" placeholder="Duration (min)" value={editMovieForm.duration} onChange={this.handleEditMovieChange} className="movie-detail-input" />
                            <input type="date" name="release_date" value={editMovieForm.release_date} onChange={this.handleEditMovieChange} className="movie-detail-input" />
                            <input type="number" name="rating" placeholder="Rating (0-10)" value={editMovieForm.rating} onChange={this.handleEditMovieChange} step="0.1" min="0" max="10" className="movie-detail-input" />
                            <input type="file" name="poster" onChange={this.handleEditMovieChange} accept="image/*" className="movie-detail-file-input" />
                            {movie.poster && <img src={`/storage/${movie.poster}`} alt="poster" className="movie-detail-preview-image" />}
                            <select name="status" value={editMovieForm.status} onChange={this.handleEditMovieChange} className="movie-detail-select">
                                <option value="now_showing">Now Showing</option>
                                <option value="coming_soon">Coming Soon</option>
                                <option value="draft">Draft</option>
                            </select>
                            {editMovieForm.status === 'coming_soon' && (
                                <input type="date" name="expected_release" value={editMovieForm.expected_release} onChange={this.handleEditMovieChange} className="movie-detail-input" />
                            )}
                            <div className="movie-detail-form-buttons">
                                <button onClick={this.updateMovie} className="movie-detail-btn-save">Save Changes</button>
                                <button onClick={this.closeEditMovieModal} className="movie-detail-btn-cancel">Cancel</button>
                            </div>
                        </div>
                    </div>
                )}

                {showtimeModal && (
                    <div className="movie-detail-modal-overlay">
                        <div className="movie-detail-modal-container-sm">
                            <div className="movie-detail-modal-header">
                                <h3 className="movie-detail-modal-title">{editingShowtime ? 'Edit Showtime' : 'Add Showtime'}</h3>
                                <button onClick={this.closeShowtimeModal} className="movie-detail-modal-close">&times;</button>
                            </div>
                            
                            <select 
                                name="cinema_id" 
                                value={showtimeForm.cinema_id} 
                                onChange={this.handleShowtimeChange}
                                className="movie-detail-select"
                            >
                                <option value="">Select Cinema</option>
                                {cinemas && cinemas.length > 0 ? (
                                    cinemas.map(cinema => (
                                        <option key={cinema.id} value={cinema.id}>
                                            {cinema.name}
                                        </option>
                                    ))
                                ) : (
                                    <option disabled>Loading cinemas...</option>
                                )}
                            </select>
                            
                            <input type="text" name="hall" placeholder="Hall" value={showtimeForm.hall} onChange={this.handleShowtimeChange} className="movie-detail-input" />
                            
                            <select name="format" value={showtimeForm.format} onChange={this.handleShowtimeChange} className="movie-detail-select">
                                <option value="2d">2D</option>
                                <option value="3d">3D</option>
                                <option value="Imax">Imax</option>
                                <option value="Beanie">Beanie</option>
                            </select>
                            
                            <input type="date" name="date" value={showtimeForm.date} onChange={this.handleShowtimeChange} className="movie-detail-input" />
                            <input type="time" name="time" value={showtimeForm.time} onChange={this.handleShowtimeChange} className="movie-detail-input" />
                            
                            <div className="movie-detail-form-buttons">
                                <button onClick={editingShowtime ? this.updateShowtime : this.addShowtime} className="movie-detail-btn-save">
                                    {editingShowtime ? 'Save Changes' : 'Add Showtime'}
                                </button>
                                <button onClick={this.closeShowtimeModal} className="movie-detail-btn-cancel">Cancel</button>
                            </div>
                        </div>
                    </div>
                )}

                {reviewModal && (
                    <div className="movie-detail-modal-overlay">
                        <div className="movie-detail-modal-container-sm">
                            <div className="movie-detail-modal-header-center">
                                <h3 className="movie-detail-modal-title-lg">
                                    {this.state.editingReview ? 'Edit Review' : 'Write a Review'}
                                </h3>
                                <button onClick={this.closeReviewModal} className="movie-detail-modal-close-lg">
                                    &times;
                                </button>
                            </div>

                            <form onSubmit={this.submitReview}>
                                <div style={{ marginBottom: '1.5rem' }}>
                                    <label className="movie-detail-review-rating-label">Rating *</label>
                                    <div className="movie-detail-review-stars-container">
                                        {this.renderStars()}
                                    </div>
                                </div>

                                <div style={{ marginBottom: '1rem' }}>
                                    <label className="movie-detail-review-label">Title</label>
                                    <input type="text" value={reviewTitle} onChange={e => this.setState({ reviewTitle: e.target.value })}
                                        placeholder="Summarize your experience"
                                        className="movie-detail-input" />
                                </div>

                                <div style={{ marginBottom: '1rem' }}>
                                    <label className="movie-detail-review-label">Review *</label>
                                    <textarea rows="5" value={reviewContent} onChange={e => this.setState({ reviewContent: e.target.value })}
                                        placeholder="What did you think about this movie?"
                                        className="movie-detail-textarea" />
                                </div>

                                <label className="movie-detail-checkbox-label">
                                    <input type="checkbox" checked={reviewIsAnonymous} onChange={e => this.setState({ reviewIsAnonymous: e.target.checked })} />
                                    Post anonymously
                                </label>

                                <div className="movie-detail-form-buttons">
                                    <button type="submit" className="movie-detail-btn-submit">
                                        {this.state.editingReview ? 'Update Review' : 'Submit Review'}
                                    </button>
                                    <button type="button" onClick={this.closeReviewModal} className="movie-detail-btn-cancel-gray">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        );
    }
}

const mountElement = document.getElementById('movieDetail');
if (mountElement) {
    ReactDOM.render(<MovieDetail />, mountElement);
}

export default MovieDetail;