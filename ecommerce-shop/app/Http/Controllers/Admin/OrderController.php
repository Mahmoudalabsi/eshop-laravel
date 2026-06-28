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
                $query->select('id', 'order_id', 'product_id', 'quantity', 'price', 'size', 'color');
            },
            'items.product:id,name'
        ])->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح',
            'new_status' => $request->status
        ]);
    }
}
