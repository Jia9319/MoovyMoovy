<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Cinema list for React dropdown (must be BEFORE resource route)
Route::get('/cinemas/list', [CinemaController::class, 'getList']);

// Movie routes
Route::get('/movies/coming-soon', [MovieController::class, 'comingSoon'])->name('movies.coming-soon');
Route::resource('movies', MovieController::class);

Route::resource('cinemas', CinemaController::class)->only(['index', 'show']);
Route::get('/cinemas/search', [CinemaController::class, 'search'])->name('cinemas.search');

// Showtime routes
Route::resource('showtimes', ShowtimeController::class);

Route::middleware('auth')->group(function () {
    Route::get('/booking/history', [BookingController::class, 'history'])->name('bookings.history');
    Route::get('/booking/ticket/{id}', [BookingController::class, 'showTicket'])->name('bookings.show');
    Route::post('/booking/ticket/{id}/cancel', [BookingController::class, 'cancelTicket'])->name('bookings.cancel');

<<<<<<< HEAD
=======
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('select', [BookingController::class, 'select'])->name('select');
        Route::get('seat', [BookingController::class, 'seat'])->name('seat');
        Route::get('food', [BookingController::class, 'food'])->name('food');
        Route::get('payment', [BookingController::class, 'payment'])->name('payment');
        Route::post('ticket', [BookingController::class, 'ticket'])->name('ticket');
        Route::get('summary', [BookingController::class, 'summary'])->name('summary');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/reviews', fn() => view('profile.reviews'))->name('reviews');
        Route::get('/details/{id}', [BookingController::class, 'showDetails'])->name('details');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/reports', [AdminController::class, 'viewReports'])->name('reports');
    });
});

>>>>>>> 232664b70b1a11c398655d2d56352bb17d1806b0
Route::post('/offers/redeem', [OfferController::class, 'redeem'])->name('offers.redeem');
Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
Route::get('/offers/{id}/claim', [OfferController::class, 'claim'])->name('offers.claim');
Route::post('/offers/apply', [OfferController::class, 'apply'])->name('offers.apply');

Route::post('/movies/{movie}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');
Route::post('/reviews/{review}/report', [ReviewController::class, 'report'])->name('reviews.report');

<<<<<<< HEAD
// Movie data for React (returns JSON)
Route::resource('movies', MovieController::class);
Route::get('/movies/{id}/data', [MovieController::class, 'show']);

// Protected routes for React (admin only)
Route::middleware(['auth'])->group(function () {
    Route::post('/movies/{movieId}/showtimes', [ShowtimeController::class, 'store']);
    Route::put('/showtimes/{id}', [ShowtimeController::class, 'update']);
    Route::delete('/showtimes/{id}', [ShowtimeController::class, 'destroy']);
    
    Route::post('/movies/{movieId}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
=======
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

>>>>>>> 232664b70b1a11c398655d2d56352bb17d1806b0
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/api/movies-status', [App\Http\Controllers\WatchlistController::class, 'getMoviesStatus'])->name('api.movies.status');

<<<<<<< HEAD
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/reviews', fn() => view('profile.reviews'))->name('profile.reviews');
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/reports', [AdminController::class, 'viewReports'])->name('admin.reports');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
=======
Route::post('/watchlist/toggle/{movieId}', [App\Http\Controllers\WatchlistController::class, 'toggle'])->name('watchlist.toggle');

Route::post('/watchlist/add/{movieId}', [App\Http\Controllers\WatchlistController::class, 'store'])->name('watchlist.add');
Route::delete('/watchlist/remove/{movieId}', [App\Http\Controllers\WatchlistController::class, 'destroy'])->name('watchlist.remove');
>>>>>>> 232664b70b1a11c398655d2d56352bb17d1806b0
