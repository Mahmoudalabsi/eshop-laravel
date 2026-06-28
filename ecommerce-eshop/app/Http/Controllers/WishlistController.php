<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $products = collect([]);

        if (Auth::check()) {
            $products = Wishlist::with(['product.subcategory.category', 'product.images'])
                ->where('user_id', Auth::id())
                ->get()
                ->map(function ($w) {
                    $p = $w->product;
                    if (!$p) return null;
                    return (object) [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => (float) $p->price,
                        'old_price' => $p->old_price ? (float) $p->old_price : null,
                        'image' => $p->image,
                        'image_url' => $p->image,
                        'discounted_price' => $p->discounted_price,
                        'stock_status' => (object) [
                            'available' => ($p->total_stock ?? 0) > 0,
                            'total_qty' => (int) ($p->total_stock ?? 0),
                        ],
                    ];
                })
                ->filter();
        }

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'added' => false,
                'message' => 'يرجى تسجيل الدخول لتتمكن من إضافة المنتجات للمفضلة'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'added' => false,
                'message' => 'تمت الإزالة من المفضلة'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'added' => true,
            'message' => 'تمت الإضافة للمفضلة'
        ]);
    }
}
