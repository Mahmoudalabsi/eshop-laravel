<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Review model (ecommerce-shop / admin backend)
 *
 * Kept in sync with the storefront so the SetupController can persist
 * all review columns (title, content, is_verified, helpful_count, status)
 * without silent data loss.
 */
class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'title',
        'content',
        'comment',
        'is_verified',
        'helpful_count',
        'status',
    ];

    protected $casts = [
        'is_verified'   => 'boolean',
        'helpful_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
