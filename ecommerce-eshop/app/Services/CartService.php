<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    const CART_SESSION_KEY = 'cart';
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Add product to cart
     */
    public function add($productId, $quantity = 1, $options = [])
    {
        // Get product from API
        $product = $this->api->get("/products/$productId")->get('data');

        if (!$product) {
            throw new \Exception('المنتج غير موجود');
        }

        $cart = $this->get();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'name' => $product['name'] ?? $product->name ?? 'منتج بدون اسم',
                'price' => $product['price'] ?? $product->price ?? 0,
                'discounted_price' => $product['discounted_price'] ?? $product->discounted_price ?? null,
                'image' => $product['image'] ?? $product->image ?? null,
                'slug' => $product['slug'] ?? $product->slug ?? null,
                'quantity' => $quantity,
                'options' => $options
            ];
        }

        $this->save($cart);
        return $this->get();
    }

    /**
     * Remove product from cart
     */
    public function remove($productId)
    {
        $cart = $this->get();
        unset($cart[$productId]);
        $this->save($cart);
        return $this->get();
    }

    /**
     * Update product quantity
     */
    public function update($productId, $quantity)
    {
        $cart = $this->get();

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        $this->save($cart);
        return $this->get();
    }

    /**
     * Get all cart items
     */
    public function get()
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    /**
     * Get cart total
     */
    public function getTotal()
    {
        $cart = $this->get();
        $total = 0;

        foreach ($cart as $item) {
            $price = $item['discounted_price'] ?? $item['price'];
            $total += $price * $item['quantity'];
        }

        return $total;
    }

    /**
     * Get cart item count
     */
    public function count()
    {
        return count($this->get());
    }

    /**
     * Get cart items count (total quantities)
     */
    public function totalQuantity()
    {
        $cart = $this->get();
        $quantity = 0;

        foreach ($cart as $item) {
            $quantity += $item['quantity'];
        }

        return $quantity;
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    /**
     * Save cart to session
     */
    private function save($cart)
    {
        Session::put(self::CART_SESSION_KEY, $cart);
    }

    /**
     * Validate cart items stock
     */
    public function validateStock()
    {
        $cart = $this->get();
        $errors = [];

        foreach ($cart as $productId => $item) {
            try {
                $response = $this->api->get("/products/$productId");
                $product = $response->get('data');

                if (!$product) {
                    $message = __('messages.product_not_available_quantity', ['product' => $item['name']]);
                    $errors[$productId] = $message;
                    continue;
                }

                // Get stock from either array or object format
                $stock = 0;
                if (is_array($product)) {
                    if (isset($product['stock'])) {
                        $stock = $product['stock'];
                    } elseif (isset($product['stock_status']['total_qty'])) {
                        $stock = $product['stock_status']['total_qty'];
                    }
                } else if (is_object($product)) {
                    if (isset($product->stock)) {
                        $stock = $product->stock;
                    } elseif (isset($product->stock_status->total_qty)) {
                        $stock = $product->stock_status->total_qty;
                    }
                }

                // Validate quantity
                if ((int)$stock < (int)$item['quantity']) {
                    $message = __('messages.product_not_available_quantity', ['product' => $item['name']]);
                    $errors[$productId] = $message;
                }
            } catch (\Exception $e) {
                // If API fails, consider product unavailable
                $message = __('messages.product_not_available_quantity', ['product' => $item['name']]);
                $errors[$productId] = $message;
            }
        }

        return $errors;
    }
}
