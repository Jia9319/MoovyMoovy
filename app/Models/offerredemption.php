<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferRedemption extends Model
{
    public $timestamps = false;

    protected $fillable = ['offer_id', 'user_id', 'redeemed_at'];

    protected $casts = ['redeemed_at' => 'datetime'];

    public function offer() { return $this->belongsTo(Offer::class); }
    public function user()  { return $this->belongsTo(User::class); }
}