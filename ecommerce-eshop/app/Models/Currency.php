<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true)->first();
    }

    public function convert($amount, $fromCurrency = null)
    {
        $from = $fromCurrency ? self::where('code', $fromCurrency)->first() : self::primary();
        if (!$from) return $amount;

        return ($amount / $from->exchange_rate) * $this->exchange_rate;
    }
}
