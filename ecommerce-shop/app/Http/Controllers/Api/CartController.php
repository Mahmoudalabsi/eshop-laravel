<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // جلب محتويات السلة للمستخدم المسجل
    public function index()
    {
        $cartItems = CartItem::with(['product'])
            ->where('user_id', Auth::id())
            ->get();

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'data' => $cartItems,
            'summary' => [
                'total_items' => $cartItems->sum('quantity'),
                'total_price' => $totalPrice,
                'currency' => 'SAR'
            ]
        ]);
    }

    // إضافة منتج للسلة (أو زيادة الكمية إذا كان موجوداً)
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'required|string',
            'size' => 'required|string'
        ]);

        // 1. التأكد من توفر المزيج (اللون + المقاس) والكمية في المخزون
        $attribute = ProductAttribute::where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if (!$attribute) {
            return response()->json(['message' => 'هذا اللون والمقاس غير متوفرين لهذا المنتج'], 422);
        }

        if ($attribute->qty < $request->quantity) {
            return response()->json(['message' => "الكمية المطلوبة غير متوفرة، المتبقي: {$attribute->qty}"], 422);
        }

        // 2. تحديث السلة مع مراعاة اللون والمقاس كفواصل فريدة
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'size' => $request->size,
                'color' => $request->color,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'تمت الإضافة للسلة بنجاح']);
    }

    // حذف منتج من السلة
    public function destroy($id)
    {
        CartItem::where('user_id', Auth::id())->where('id', $id)->delete();
        return response()->json(['message' => 'تم الحذف من السلة']);
    }
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);

        // تحقق من المخزون مرة أخرى قبل التحديث
        $attribute = ProductAttribute::where('product_id', $cartItem->product_id)
            ->where('size', $cartItem->size)
            ->where('color', $cartItem->color)
            ->first();

        if ($attribute->qty < $request->quantity) {
            return response()->json(['message' => 'الكمية المطلوبة غير متوفرة في المخزون'], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'تم تحديث الكمية بنجاح']);
    }
    // public function checkout(Request $request)
    // {
    //     $cartItems = CartItem::where('user_id', auth()->id())->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json(['message' => 'سلتك فارغة حالياً!'], 400);
    //     }

    //     $totalPrice = $cartItems->sum(function ($item) {
    //         return $item->product->price * $item->quantity;
    //     });

    //     $order = Order::create([
    //         'user_id' => auth()->id(),
    //         'total_price' => $totalPrice,
    //         'status' => 'pending',
    //         'address' => $request->address ?? 'لا يوجد عنوان',
    //     ]);

    //     foreach ($cartItems as $item) {
    //         $order->items()->create([
    //             'product_id' => $item->product_id,
    //             'quantity' => $item->quantity,
    //             'price' => $item->product->price,
    //             'size' => $item->size,
    //             'color' => $item->color,
    //         ]);

    //         ProductAttribute::where('product_id', $item->product_id)
    //             ->where('size', $item->size)
    //             ->where('color', $item->color)
    //             ->decrement('qty', $item->quantity);
    //     }

    //     CartItem::where('user_id', auth()->id())->delete();

    //     return response()->json([
    //         'message' => 'تم إتمام الطلب بنجاح!',
    //         'order_id' => $order->id
    //     ], 201);
    // }
}
