<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create order from cart — writes to local DB.
     */
    public function createFromCart(int $userId, array $data): object
    {
        $cart = $this->cartService->get();

        if (empty($cart)) {
            throw new \Exception(__('messages.empty_cart_error'));
        }

        $errors = $this->cartService->validateStock();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        $subtotal = $this->cartService->getTotal();
        $tax = $subtotal * 0.15;
        $shipping = (float) ($data['shipping_cost'] ?? 50);
        $total = $subtotal + $tax + $shipping;

        $order = Order::create([
            'user_id'         => $userId,
            'order_number'    => 'ORD-' . strtoupper(Str::random(10)),
            'status'          => 'pending',
            'payment_status'  => $data['payment_method'] === 'cash_on_delivery' ? 'unpaid' : 'pending',
            'subtotal'        => $subtotal,
            'tax'             => $tax,
            'shipping_cost'   => $shipping,
            'total'           => $total,
            'currency_code'   => $data['currency_code'] ?? 'SAR',
            'customer_name'   => $data['customer_name'],
            'customer_email'  => $data['customer_email'],
            'customer_phone'  => $data['customer_phone'],
            'shipping_address'=> is_array($data['shipping_address'] ?? null) ? json_encode($data['shipping_address']) : ($data['shipping_address'] ?? null),
            'billing_address' => is_array($data['billing_address'] ?? null) ? json_encode($data['billing_address']) : ($data['billing_address'] ?? null),
            'notes'           => $data['notes'] ?? null,
            'payment_method'  => $data['payment_method'] ?? 'cash_on_delivery',
        ]);

        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id'    => $order->id,
                'product_id'  => $productId,
                'product_name'=> $item['name'],
                'quantity'    => $item['quantity'],
                'price'       => $item['discounted_price'] ?? $item['price'],
                'unit_price'  => $item['discounted_price'] ?? $item['price'],
                'total_price' => ($item['discounted_price'] ?? $item['price']) * $item['quantity'],
                'size'        => $item['options']['size'] ?? null,
                'color'       => $item['options']['color'] ?? null,
                'attributes'  => isset($item['options']) ? json_encode($item['options']) : null,
            ]);
        }

        $this->cartService->clear();

        // Return enriched order object for view compatibility
        $order->load('items');
        return (object) $order->toArray();
    }

    public function getOrder($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            throw new \Exception(__('messages.order_not_found'));
        }
        return (object) $order->toArray();
    }

    public function getUserOrders($userId, $params = [])
    {
        $perPage = (int) ($params['per_page'] ?? 10);
        $page = (int) ($params['page'] ?? request()->input('page', 1));

        $query = Order::with('items')->where('user_id', $userId)->latest();
        $total = $query->count();
        $items = $query->forPage($page, $perPage)->get();

        return collect([
            'items' => $items->map(fn($o) => (object) $o->toArray()),
            'meta' => (object) [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::find($orderId);
        if (!$order) {
            throw new \Exception('الطلب غير موجود');
        }
        $order->update(['status' => $status]);
        return (object) $order->toArray();
    }

    public function cancel($orderId)
    {
        return $this->updateStatus($orderId, 'cancelled');
    }

    public function getStatistics()
    {
        return [
            'total_orders'     => Order::count(),
            'total_revenue'    => Order::where('payment_status', 'paid')->sum('total'),
            'pending_orders'   => Order::where('status', 'pending')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'avg_order_value'  => Order::avg('total') ?? 0,
        ];
    }
}
