<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\OrderService;
use App\Models\Order;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $orderService;

    public function __construct(CartService $cartService, OrderService $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }

    public function index()
    {
        $cart = $this->cartService->get();

        if (empty($cart)) {
            return redirect()->route('cart.index')
                          ->with('error', __('messages.empty_cart'));
        }

        $cartTotal = $this->cartService->getTotal();
        $tax = $cartTotal * 0.15;
        $shipping = 50;

        return view('checkout.index', compact('cart', 'cartTotal', 'tax', 'shipping'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email',
                'customer_phone' => 'required|string|max:20',
                'shipping_address' => 'required|string',
                'city' => 'required|string',
                'postal_code' => 'required|string',
                'billing_address' => 'nullable|string',
                'shipping_cost' => 'numeric|min:0',
                'notes' => 'nullable|string',
                'payment_method' => 'required|in:credit_card,debit_card,bank_transfer,cash_on_delivery'
            ]);

            $validated['shipping_address'] = [
                'address' => $validated['shipping_address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code']
            ];

            $validated['billing_address'] = $validated['billing_address'] ?? null;
            $validated['currency_code'] = session('currency', 'SAR');

            $order = $this->orderService->createFromCart(auth()->id(), $validated);

            return redirect()->route('checkout.success', $order->id)
                          ->with('success', __('messages.order_created_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                          ->with('error', $e->getMessage())
                          ->withInput();
        }
    }

    public function success($orderId)
    {
        $order = Order::with('items')->find($orderId);

        if (!$order || $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    public function paymentCallback(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $status = $request->input('status');
            $order = Order::find($orderId);

            if (!$order) {
                return redirect()->route('home')->with('error', __('messages.order_not_found'));
            }

            if ($status === 'success') {
                $order->update(['payment_status' => 'paid']);
                return redirect()->route('orders.show', $orderId)
                              ->with('success', __('messages.payment_success'));
            } else {
                return redirect()->route('checkout.index')
                              ->with('error', __('messages.payment_failed'));
            }
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')
                          ->with('error', __('messages.error_occurred') . $e->getMessage());
        }
    }
}
