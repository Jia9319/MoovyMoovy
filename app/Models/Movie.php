<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'genre',
        'duration',
        'release_date',
        'rating',
        'poster',
        'status',
        'expected_release',
    ];

    protected $casts = [
        'release_date' => 'date',
        'expected_release' => 'date',
        'rating' => 'decimal:1',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeNowShowing($query)
    {
        return $query->where('status', 'now_showing');
    }

    public function scopeComingSoon($query)
    {
        return $query->where('status', 'coming_soon');
    }

    public function getPosterUrlAttribute(): string
    {
        return $this->poster ? asset('storage/' . $this->poster) : '';
    }

    public function getStarRatingAttribute()
    {
        $rating = $this->rating ?? 0;
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $stars = '';
        for ($i = 0; $i < $fullStars; $i++) {
            $stars .= '<i class="fas fa-star"></i>';
        }
        if ($halfStar) {
            $stars .= '<i class="fas fa-star-half-alt"></i>';
        }
        for ($i = 0; $i < $emptyStars; $i++) {
            $stars .= '<i class="far fa-star"></i>';
        }

        return $stars;
    }

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

    public function users()
    {
        return $this->belongsToMany(User::class, 'movie_user');
    }
}