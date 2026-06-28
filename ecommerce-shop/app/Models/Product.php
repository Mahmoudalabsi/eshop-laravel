<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // 1. تحديث Fillable: استبدل category_id بـ subcategory_id
    protected $fillable = [
        'name',
        'description',
        'price',
        'old_price',
        'subcategory_id', // الحقل الجديد
        'image',
        'total_stock',
        'status'
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
    // protected static function booted()
    // {
    //     static::addGlobalScope('activeCategory', function ($builder) {
    //         $builder->whereHas('category', function ($query) {
    //             $query->where('status', 1);
    //         });
    //     });
    // }
    // خاصية لتعرف إذا كان القسم نشطاً أم لا

}