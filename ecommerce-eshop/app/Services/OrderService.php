<?php

namespace App\Services;

class OrderService
{
    protected $cartService;
    protected $api;

    public function __construct(CartService $cartService, ApiService $api)
    {
        $this->cartService = $cartService;
        $this->api = $api;
    }

    /**
     * Create order from cart
     */
    public function createFromCart(int $userId, array $data): object
    {
        $cart = $this->cartService->get();

        if (empty($cart)) {
            throw new \Exception(__('messages.empty_cart_error'));
        }

        // Validate stock
        $errors = $this->cartService->validateStock();

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        // Prepare order payload
        $orderData = [
            'user_id' => $userId,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'],
            'shipping_address' => $data['shipping_address'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_cost' => $data['shipping_cost'] ?? 0,
            'currency_code' => $data['currency_code'] ?? 'SAR',
            'notes' => $data['notes'] ?? null,
            'items' => array_map(function ($productId, $item) {
                return [
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['discounted_price'] ?? $item['price'],
                    'attributes' => $item['options'] ?? null
                ];
            }, array_keys($cart), $cart)
        ];

        // Send to API
        try {
            $response = $this->api->post('/orders', $orderData);
        } catch (\Exception $e) {
            throw $e;
        }

        if ($response->get('error')) {
            $errorMsg = $response->get('message', __('messages.order_creation_error'));
            throw new \Exception($errorMsg);
        }

        $responseData = $response->get('data');

        if ($responseData) {
            $this->cartService->clear();
        }

        return (object) $responseData;
    }

    /**
     * Get order details
     */
    public function getOrder($id)
    {
        $response = $this->api->get("/orders/$id");

        if ($response->get('error')) {
            throw new \Exception(__('messages.order_not_found'));
        }

        return (object) $response->get('data');
    }

    /**
     * Get user orders
     */
    public function getUserOrders($userId, $params = [])
    {
        $response = $this->api->get('/orders', array_merge($params, ['user_id' => $userId]));
        $data = $response->get('data', []);

        return collect($data)->map(function ($item) {
            return (object) $item;
        });
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status)
    {
        $response = $this->api->post("/orders/$orderId/status", ['status' => $status]);

        if ($response->get('error')) {
            throw new \Exception('فشل تحديث الحالة');
        }

        return (object) $response->get('data');
    }

    /**
     * Cancel order
     */
    public function cancel($orderId)
    {
        return $this->updateStatus($orderId, 'cancelled');
    }

    /**
     * Get order statistics
     */
    public function getStatistics()
    {
        $response = $this->api->get('/orders/statistics');

        if ($response->get('error')) {
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'delivered_orders' => 0,
                'avg_order_value' => 0,
            ];
        }

        return $response->get('data', []);
    }
}
