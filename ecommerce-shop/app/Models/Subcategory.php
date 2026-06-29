<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Subcategory model (ecommerce-shop / admin backend)
 *
 * Kept in sync with the storefront so the SetupController can persist
 * slug/description/image fields without silent data loss.
 */
class Subcategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
