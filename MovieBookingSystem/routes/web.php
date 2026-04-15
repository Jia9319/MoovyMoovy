<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AdminController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Movies
Route::resource('movies', MovieController::class);

// Cinemas (read only)
Route::resource('cinemas', CinemaController::class)->only(['index', 'show']);

// Showtimes
Route::resource('showtimes', ShowtimeController::class);

// Reviews
Route::post('/movies/{movie}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like');
Route::post('/reviews/{review}/report', [ReviewController::class, 'report'])->name('reviews.report');

// Auth
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Protected — logged in users
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Booking
    Route::get('/booking/select', [BookingController::class, 'select'])->name('booking.select');
    Route::get('/booking/seat', [BookingController::class, 'seat'])->name('booking.seat');
    Route::get('/booking/food', [BookingController::class, 'food'])->name('booking.food');
    Route::get('/booking/payment', [BookingController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/ticket', [BookingController::class, 'ticket'])->name('booking.ticket');
    Route::get('/booking/summary', [BookingController::class, 'summary'])->name('booking.summary');
});

// Protected — admin only
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/reports', [AdminController::class, 'viewReports'])->name('admin.reports');
    Route::get('/movies', [AdminController::class, 'movies'])->name('admin.movies');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::delete('/reviews/{review}', [AdminController::class, 'deleteReview'])->name('admin.reviews.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
});