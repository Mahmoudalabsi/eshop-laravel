<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Order model (ecommerce-shop / admin backend)
 *
 * Kept in sync with the storefront (ecommerce-eshop) Order model so both apps
 * share the same $fillable set, including tax, payment_method, tracking_number,
 * shipped_at, and delivered_at. Without this, the admin's order update flow
 * would silently drop these fields.
 */
class Order extends Model
{
    use SoftDeletes;

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
        'tax',
        'total',
        'total_price',
        'currency_code',
        'status',
        'payment_status',
        'payment_method',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
        'shipped_at'       => 'datetime',
        'delivered_at'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    // Methods
    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsShipped($trackingNumber = null)
    {
        $this->update([
            'status'           => 'shipped',
            'tracking_number'  => $trackingNumber,
            'shipped_at'       => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsPaid()
    {
        $this->update(['payment_status' => 'paid']);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending'     => '<span class="badge bg-warning">قيد الانتظار</span>',
            'processing'  => '<span class="badge bg-info">قيد المعالجة</span>',
            'shipped'     => '<span class="badge bg-primary">تم الشحن</span>',
            'delivered'   => '<span class="badge bg-success">تم التسليم</span>',
            'completed'   => '<span class="badge bg-success">مكتمل</span>',
            'cancelled'   => '<span class="badge bg-danger">ملغاة</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">غير معروف</span>';
    }
}
