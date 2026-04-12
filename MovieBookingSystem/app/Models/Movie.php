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

    // ── Relationships ──────────────────────────────────────

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── Accessors ──────────────────────────────────────────

    /**
     * Full URL to the poster image, or empty string if none.
     */
    public function getPosterUrlAttribute(): string
    {
        return $this->poster ? asset('storage/' . $this->poster) : '';
    }

    /**
     * Unique gradient based on the movie's ID so every card
     * without a poster gets a visually distinct background.
     */
    public function getGradientAttribute(): string
    {
        $gradients = [
            'linear-gradient(145deg,#1a0033,#660094)',
            'linear-gradient(145deg,#200044,#9614d0)',
            'linear-gradient(145deg,#100020,#bb44f0)',
            'linear-gradient(145deg,#0d0020,#4a0080)',
            'linear-gradient(145deg,#1a0040,#5500aa)',
            'linear-gradient(145deg,#08000f,#9614d0)',
            'linear-gradient(145deg,#0a001a,#7700cc)',
            'linear-gradient(145deg,#180030,#440088)',
        ];

        return $gradients[$this->id % count($gradients)];
    }
}