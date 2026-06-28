<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\CartService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $cartService;

    public function __construct(OrderService $orderService, CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    public function index()
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function invoice($id)
    {
        $order = Order::with('items')->findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('orders.invoice', compact('order'));
    }

    public function cancel(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            if ($order->user_id !== auth()->id()) {
                abort(403);
            }

            if (!in_array($order->status, ['pending', 'processing'])) {
                return redirect()->back()
                              ->with('error', __('messages.cannot_cancel_order'));
            }

            $order->update(['status' => 'cancelled']);

            return redirect()->back()
                          ->with('success', __('messages.order_cancelled_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                          ->with('error', __('messages.error_occurred') . $e->getMessage());
        }
    }
}
