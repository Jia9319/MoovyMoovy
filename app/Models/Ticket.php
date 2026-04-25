<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'movie_id',
    'showtime_id',
    'ticket_code',
    'qr_url',
    'cinema',
    'hall',
    'format',
    'date',
    'time',
    'seats',
    'seat_count',
    'seat_total',
    'food_lines',
    'food_total',
    'grand_total',
    'payment_method',
    'status',
];

    protected $casts = [
        'date' => 'date',
        'seats' => 'array',
        'food_lines' => 'array',
        'seat_total' => 'decimal:2',
        'food_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }
}

