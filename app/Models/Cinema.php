<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'address', 'city', 'state', 'postal_code',
        'phone', 'email', 'description', 'facilities', 'image',
        'latitude', 'longitude', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    public function getFacilitiesArrayAttribute()
    {
        return $this->facilities ? explode(',', $this->facilities) : [];
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}