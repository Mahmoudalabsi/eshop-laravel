<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * OrderItem model (ecommerce-shop / admin backend)
 *
 * Kept in sync with the storefront (ecommerce-eshop) OrderItem model so both
 * apps share the same $fillable set, including sku, size, color, and attributes.
 */
class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'price',
        'sku',
        'size',
        'color',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'json',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
