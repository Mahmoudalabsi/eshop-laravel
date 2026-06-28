<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'status', 'size_guide_id'];

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