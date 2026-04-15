<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'genre', 'duration',
        'release_date', 'rating', 'poster',
    ];

    protected $casts = [
        'release_date' => 'date',
        'rating'       => 'decimal:1',
    ];

    public function bookings()
    {
        return $this->hasManyThrough(
            \App\Models\Booking::class,
            \App\Models\Showtime::class,
            'movie_id',   // foreign key on showtimes
            'showtime_id' // foreign key on bookings
        );
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper: poster URL or placeholder gradient
    public function getPosterUrlAttribute(): string
    {
        return $this->poster
            ? asset('storage/' . $this->poster)
            : '';
    }
}