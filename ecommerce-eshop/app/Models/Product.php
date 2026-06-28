<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

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
        'discount_percentage'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_on_offer' => 'boolean',
        'dimensions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function category()
    {
        return $this->hasOneThrough(Category::class, Subcategory::class, 'id', 'id', 'subcategory_id', 'category_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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

    // Accessors
    public function getDiscountedPriceAttribute()
    {
        if ($this->is_on_offer && $this->discount_percentage) {
            return $this->price - ($this->price * $this->discount_percentage / 100);
        }
        return $this->price;
    }

    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
    // protected static function booted()
    // {
    //     static::addGlobalScope('active', function ($builder) {
    //         $builder->where('status', 'active') // للمنتج نفسه
    //             ->whereHas('subcategory', function ($q) {
    //                 $q->where('status', 'active') // للقسم الفرعي
    //                     ->whereHas('category', function ($q2) {
    //                         $q2->where('status', 1); // للقسم الرئيسي
    //                     });
    //             });
    //     });
    // }
}
