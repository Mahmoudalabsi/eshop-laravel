<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        return view('eshop.dashboard.orders', compact('orders'));
    }

    public function getOrdersJson()
    {
        // جلب الطلب مع المستخدم، العناصر، وتفاصيل المنتج لكل عنصر
        $orders = Order::with([
            'user',
            'items' => function ($query) {
                $query->select('id', 'order_id', 'product_id', 'quantity', 'price', 'unit_price', 'total_price', 'size', 'color', 'product_name');
            },
            'items.product:id,name'
        ])
        ->select('id', 'user_id', 'order_number', 'status', 'payment_status', 'total_price', 'total', 'subtotal', 'tax', 'shipping_cost', 'currency_code', 'customer_name', 'created_at')
        ->latest()
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);

        // تحديث الحقول الإضافية حسب الحالة الجديدة
        $updateData = ['status' => $request->status];

        switch ($request->status) {
            case 'shipped':
                if (empty($order->tracking_number)) {
                    $updateData['tracking_number'] = 'TRK-' . strtoupper(\Illuminate\Support\Str::random(10));
                }
                if (empty($order->shipped_at)) {
                    $updateData['shipped_at'] = now();
                }
                break;
            case 'delivered':
                if (empty($order->delivered_at)) {
                    $updateData['delivered_at'] = now();
                }
                break;
            case 'completed':
                $updateData['payment_status'] = 'paid';
                break;
            case 'cancelled':
                $updateData['payment_status'] = 'cancelled';
                break;
        }

        $order->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح',
            'new_status' => $request->status,
            'tracking_number' => $order->fresh()->tracking_number,
        ]);
    }
}
