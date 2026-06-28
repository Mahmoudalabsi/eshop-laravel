<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use App\Models\Product;

class CartService
{
    const CART_SESSION_KEY = 'cart';

    /**
     * Add product to cart by ID — uses local Eloquent model.
     */
    public function add($productId, $quantity = 1, $options = [])
    {
        $product = Product::find($productId);

        if (!$product) {
            throw new \Exception('المنتج غير موجود');
        }

        $cart = $this->get();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => (int) $productId,
                'name' => $product->name,
                'price' => (float) $product->price,
                'discounted_price' => $product->discounted_price,
                'image' => $product->image,
                'slug' => $product->slug,
                'quantity' => (int) $quantity,
                'options' => $options,
            ];
        }

        $this->save($cart);
        return $this->get();
    }

    public function remove($productId)
    {
        $cart = $this->get();
        unset($cart[$productId]);
        $this->save($cart);
        return $this->get();
    }

    public function update($productId, $quantity)
    {
        $cart = $this->get();
        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = (int) $quantity;
            }
        }
        $this->save($cart);
        return $this->get();
    }

    public function get()
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

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

    public function count()
    {
        return count($this->get());
    }

    public function totalQuantity()
    {
        $qty = 0;
        foreach ($this->get() as $item) {
            $qty += $item['quantity'];
        }
        return $qty;
    }

    public function clear()
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    private function save($cart)
    {
        Session::put(self::CART_SESSION_KEY, $cart);
    }

    /**
     * Validate cart items against local DB stock.
     */
    public function validateStock()
    {
        $errors = [];
        foreach ($this->get() as $productId => $item) {
            $product = Product::find($productId);
            if (!$product) {
                $errors[$productId] = "المنتج ({$item['name']}) لم يعد متوفراً";
                continue;
            }
            if ((int) $product->total_stock < (int) $item['quantity']) {
                $errors[$productId] = "الكمية المطلوبة من ({$item['name']}) غير متوفرة";
            }
        }
        return $errors;
    }
}
