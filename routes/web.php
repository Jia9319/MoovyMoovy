<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/booking/select', [BookingController::class, 'select'])->name('booking.select');
Route::get('/booking/auth-status', [BookingController::class, 'authStatus'])->name('booking.auth.status');
Route::get('/booking/seat', [BookingController::class, 'seat'])->name('booking.seat');
Route::get('/booking/food', [BookingController::class, 'food'])->name('booking.food');
Route::get('/booking/payment', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/ticket', [BookingController::class, 'ticket'])->name('booking.ticket');
Route::get('/booking/summary', [BookingController::class, 'summary'])->name('booking.summary');

Route::post('/offers/redeem',  [OfferController::class, 'redeem'])->name('offers.redeem');
Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
Route::get('/offers/{id}/claim', [OfferController::class, 'claim'])->name('offers.claim');
Route::post('/offers/apply', [OfferController::class, 'apply'])->name('offers.apply');

Route::post('/movies/{movie}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');
Route::post('/reviews/{review}/report', [ReviewController::class, 'report'])->name('reviews.report');

// Movie data for React (returns JSON)
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
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/reviews', fn() => view('profile.reviews'))->name('profile.reviews');
    Route::get('/bookings', [ProfileController::class, 'history'])->name('bookings.history');
    Route::get('/bookings/{ticket}', [ProfileController::class, 'ticket'])->name('bookings.show');
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/reports', [AdminController::class, 'viewReports'])->name('admin.reports');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
