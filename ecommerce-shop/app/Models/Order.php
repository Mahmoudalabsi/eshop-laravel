<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'phone',
        'address',
        'shipping_address',
        'billing_address',
        'subtotal',
        'shipping_cost',
        'total',
        'total_price',
        'currency_code',
        'status',
        'payment_status',
        'notes'
    ];

    protected $casts = [
        'shipping_address' => 'json',
        'billing_address' => 'json'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
