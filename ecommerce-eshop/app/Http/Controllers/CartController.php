<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Models\Product;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->get();
        $total = $this->cartService->getTotal();
        $quantity = $this->cartService->totalQuantity();

        return view('cart.index', compact('cart', 'total', 'quantity'));
    }

    public function add(Request $request, $id)
    {
        try {
            $quantity = $request->input('quantity', 1);
            $options = $request->only(['color', 'size', 'variant']);

            $this->cartService->add($id, $quantity, $options);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.product_added_success'),
                    'cart_count' => $this->cartService->count(),
                    'cart_quantity' => $this->cartService->totalQuantity()
                ]);
            }

            return redirect()->back()
                          ->with('success', __('messages.product_added_success'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()
                          ->with('error', __('messages.error_occurred') . $e->getMessage());
        }
    }

    public function remove(Request $request, $id)
    {
        try {
            $this->cartService->remove($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.product_removed_success'),
                    'total' => $this->cartService->getTotal(),
                    'count' => $this->cartService->count(),
                    'quantity' => $this->cartService->totalQuantity(),
                    'final_total' => $this->cartService->getTotal() * 1.15,
                    'tax' => $this->cartService->getTotal() * 0.15,
                ]);
            }

            return redirect()->back()
                          ->with('success', __('messages.product_removed_success'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()
                          ->with('error', __('messages.error_occurred') . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required',
                'quantity' => 'required|numeric|min:1'
            ]);

            $this->cartService->update($validated['id'], $validated['quantity']);

            if ($request->ajax()) {
                $cart = $this->cartService->get();
                $item = $cart[$validated['id']] ?? null;

                return response()->json([
                    'success' => true,
                    'message' => __('messages.cart_updated_success'),
                    'total' => $this->cartService->getTotal(),
                    'count' => $this->cartService->count(),
                    'quantity' => $this->cartService->totalQuantity(),
                    'final_total' => $this->cartService->getTotal() * 1.15,
                    'tax' => $this->cartService->getTotal() * 0.15,
                    'item_total' => $item ? ($item['discounted_price'] ?? $item['price']) * $item['quantity'] : 0,
                ]);
            }

            return redirect()->back()
                          ->with('success', __('messages.cart_updated_success'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()
                          ->with('error', __('messages.error_occurred') . $e->getMessage());
        }
    }

    public function clear()
    {
        $this->cartService->clear();

        return redirect('cart')->with('success', __('messages.cart_cleared_success'));
    }

    public function getCartData()
    {
        $subtotal = $this->cartService->getTotal();

        return response()->json([
            'items' => $this->cartService->get(),
            'total' => $subtotal,
            'count' => $this->cartService->count(),
            'quantity' => $this->cartService->totalQuantity(),
            'tax' => round($subtotal * 0.15, 2),
            'final_total' => round($subtotal * 1.15, 2),
        ]);
    }
}
