<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Product model (ecommerce-shop / admin backend)
 *
 * NOTE: Kept in sync with the storefront (ecommerce-eshop) Product model so both
 * apps share the same $fillable set. Without this, the SetupController's
 * updateOrCreate() calls would silently drop slug/is_featured/is_on_offer/etc.
 * and the storefront would render products without slugs, badges, or discount info.
 */
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subcategory_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'old_price',
        'cost_price',
        'image',
        'status',
        'total_stock',
        'sku',
        'barcode',
        'weight',
        'dimensions',
        'is_featured',
        'is_on_offer',
        'discount_percentage',
        'offer_expires_at',
    ];

    protected $casts = [
        'is_featured'      => 'boolean',
        'is_on_offer'      => 'boolean',
        'dimensions'       => 'array',
        'offer_expires_at' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            Subcategory::class,
            'id',             // مفتاح القسم الفرعي
            'id',             // مفتاح القسم الرئيسي
            'subcategory_id', // المفتاح الأجنبي في المنتجات
            'category_id'     // المفتاح الأجنبي في الأقسام الفرعية
        );
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // متوسط التقييم
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?: 0;
    }

    // السعر بعد الخصم
    /**
     * The current selling price of the product.
     *
     * NOTE: In this codebase, `price` is ALREADY the discounted/sale price
     * (the merchant sets price=current price and old_price=original price).
     * The `discount_percentage` field is informational — it stores the
     * percentage delta from old_price to price, NOT an additional discount
     * to be re-applied on top of `price`.
     *
     * Earlier this accessor wrongly returned price - (price * discount / 100),
     * which double-applied the discount. Returns $this->price for parity with
     * the storefront (ecommerce-eshop) Product model.
     */
    public function getDiscountedPriceAttribute()
    {
        return (float) $this->price;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnOffer($query)
    {
        return $query->where('is_on_offer', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('total_stock', '>', 0);
    }

    public function scopeSearchByName($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }
}
