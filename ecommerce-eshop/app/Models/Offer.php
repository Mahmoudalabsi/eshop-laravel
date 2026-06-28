<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_value',
        'type',
        'scope',
        'target_id',
        'starts_at',
        'ends_at',
        'status',
        'image',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'status'    => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }
}
