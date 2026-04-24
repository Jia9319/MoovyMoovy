<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'title', 'description', 'code', 'discount_percent',
        'valid_from', 'valid_until', 'is_active',
        'max_uses', 'used_count', 'terms',
    ];

    protected $casts = [
        'valid_from'   => 'date',
        'valid_until'  => 'date',
        'is_active'    => 'boolean',
    ];

    public function redemptions()
    {
        return $this->hasMany(OfferRedemption::class);
    }
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if (now()->lt($this->valid_from))   return false;
        if (now()->gt($this->valid_until))  return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function isRedeemedBy(int $userId): bool
    {
        return $this->redemptions()->where('user_id', $userId)->exists();
    }

    // Scope: only active & within date range
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('valid_from', '<=', now())
                     ->where('valid_until', '>=', now());
    }
}