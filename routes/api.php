<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\ReviewController;


Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/coming-soon', [MovieController::class, 'comingSoon']);
Route::get('/movies/{id}', [MovieController::class, 'show']);
Route::get('/cinemas/list', [App\Http\Controllers\CinemaController::class, 'getList']);

Route::middleware('auth')->group(function () {
    
    // Movie Management
    Route::post('/movies', [MovieController::class, 'store']);
    Route::put('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);
    
    // Showtime Management  
    Route::post('/movies/{movieId}/showtimes', [ShowtimeController::class, 'store']);
    Route::put('/showtimes/{id}', [ShowtimeController::class, 'update']);
    Route::delete('/showtimes/{id}', [ShowtimeController::class, 'destroy']);
    
    // Review Management
    Route::post('/movies/{movieId}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});