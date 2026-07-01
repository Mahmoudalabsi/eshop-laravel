<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Category model (ecommerce-shop / admin backend)
 *
 * Kept in sync with the storefront so the SetupController can persist
 * the category image and status fields without silent data loss.
 */
class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'status',
        'size_guide_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope: only active categories (status = true).
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function sizeGuide()
    {
        return $this->belongsTo(SizeGuide::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products()
    {
        // جلب المنتجات عبر جدول الأقسام الفرعية
        return $this->hasManyThrough(
            Product::class,
            Subcategory::class,
            'category_id',    // المفتاح الأجنبي في جدول subcategories
            'subcategory_id', // المفتاح الأجنبي في جدول products
            'id',             // المفتاح المحلي في جدول categories
            'id'              // المفتاح المحلي في جدول subcategories
        );
    }
}
