<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'subtotal',
        'tax',
        'shipping_cost',
        'total',
        'total_price',
        'currency_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'billing_address',
        'notes',
        'payment_method',
        'tracking_number',
        'shipped_at',
        'delivered_at'
    ];

    /**
     * Accessor: total_price fallback to total.
     *
     * Many legacy / admin views read $order->total_price, but the storefront
     * OrderService only writes to `total`. Without this accessor, those views
     * would see NULL and render "0.00" or "NaN" for the order grand total.
     * Returning $this->total keeps both code paths consistent.
     */
    public function getTotalPriceAttribute($value)
    {
        return $value !== null && $value !== 0.0
            ? $value
            : ($this->total ?? 0);
    }

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
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
            'status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipped_at' => now()
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
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
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'processing' => '<span class="badge bg-info">قيد المعالجة</span>',
            'shipped' => '<span class="badge bg-primary">تم الشحن</span>',
            'delivered' => '<span class="badge bg-success">تم التسليم</span>',
            'cancelled' => '<span class="badge bg-danger">ملغاة</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">غير معروف</span>';
    }
}
