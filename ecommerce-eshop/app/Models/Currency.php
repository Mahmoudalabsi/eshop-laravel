<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'status' => 'boolean',
        'exchange_rate' => 'float',
    ];

    /**
     * Get the default currency (used for fallback).
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Convert amount from one currency to this one.
     */
    public function convert($amount, $fromCurrency = null)
    {
        $from = $fromCurrency
            ? self::where('code', $fromCurrency)->first()
            : self::where('is_default', true)->first();

        if (!$from) {
            return $amount;
        }

        return ($amount / $from->exchange_rate) * $this->exchange_rate;
    }
}
