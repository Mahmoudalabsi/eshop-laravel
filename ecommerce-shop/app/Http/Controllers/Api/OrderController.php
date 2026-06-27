<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a new order from cart
     */
    public function store(Request $request)
    {


        // Get the authenticated user
        if (!$request->user()) {

            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            // Validate request - but use authenticated user's ID
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email',
                'customer_phone' => 'required|string|max:20',
                'shipping_address' => 'required|array',
                'billing_address' => 'required|array',
                'shipping_cost' => 'numeric|min:0',
                'currency_code' => 'required|string|max:10',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.product_name' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'notes' => 'nullable|string'
            ]);

            // Use authenticated user's ID (security measure)
            $validated['user_id'] = $request->user()->id;



            // Start transaction
            $order = DB::transaction(function () use ($validated) {
                // Calculate totals
                $subtotal = 0;
                foreach ($validated['items'] as $item) {
                    $subtotal += $item['unit_price'] * $item['quantity'];
                }

                $shipping_cost = $validated['shipping_cost'] ?? 0;
                $total = $subtotal + $shipping_cost;



                // Create order
                $order = Order::create([
                    'user_id' => $validated['user_id'],
                    'order_number' => 'ORD-' . time() . '-' . $validated['user_id'],
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'shipping_address' => json_encode($validated['shipping_address']),
                    'billing_address' => json_encode($validated['billing_address']),
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping_cost,
                    'total' => $total,
                    'total_price' => $total, // Legacy column for backward compatibility
                    'currency_code' => $validated['currency_code'],
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'notes' => $validated['notes'] ?? null
                ]);



                // Create order items
                foreach ($validated['items'] as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'product_name' => $itemData['product_name'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'price' => $itemData['unit_price'], // Legacy column for backward compatibility
                        'total_price' => $itemData['unit_price'] * $itemData['quantity'],
                        'attributes' => json_encode($itemData['attributes'] ?? [])
                    ]);
                }



                return $order;
            });



            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get order details
     */
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
