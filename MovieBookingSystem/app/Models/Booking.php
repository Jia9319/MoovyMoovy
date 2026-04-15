<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'showtime_id',
        'total_price',
        'status',
        'seats',
    ];

    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Showtime::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}