<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Order;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function getCurrencies()
    {
        return response()->json([
            'data' => Currency::where('status', 1)->get()
        ]);
    }

    public function getLanguages()
    {
        return response()->json([
            'data' => \App\Models\Language::where('status', 1)->get()
        ]);
    }

    public function getProducts(Request $request)
    {
        $query = Product::with(['subcategory.category', 'images', 'attributes'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', 1);

        if ($request->has('category_id')) {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->has('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('color')) {
            $query->whereHas('attributes', function ($q) use ($request) {
                $q->where('color', $request->color);
            });
        }

        if ($request->has('size')) {
            $query->whereHas('attributes', function ($q) use ($request) {
                $q->where('size', $request->size);
            });
        }

        if ($request->has('offers')) {
            $query->whereNotNull('old_price')->where('old_price', '>', 'price');
        }

        if ($request->has('featured')) {
            $query->inRandomOrder();
        }

        if ($request->has('sort')) {
            if ($request->sort == 'top_rated') {
                $query->orderBy('reviews_avg_rating', 'desc');
            } elseif ($request->sort == 'price_low') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort == 'price_high') {
                $query->orderBy('price', 'desc');
            } elseif ($request->sort == 'newest') {
                $query->latest();
            }
        }

        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    public function getFilters()
    {
        $colors = \App\Models\ProductAttribute::whereNotNull('color')
            ->where('color', '!=', '')
            ->distinct()
            ->pluck('color');

        $sizes = \App\Models\ProductAttribute::whereNotNull('size')
            ->where('size', '!=', '')
            ->distinct()
            ->pluck('size');

        return response()->json([
            'colors' => $colors,
            'sizes' => $sizes
        ]);
    }

    public function getProduct($id)
    {
        $product = Product::with(['subcategory.category', 'images', 'attributes', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        return new ProductResource($product);
    }



    public function getCategories()
    {
        $categories = Category::with('subcategories')
            ->withCount('products')
            ->where('status', 1)
            ->get();

        return CategoryResource::collection($categories);
    }

    public function getCategory($id)
    {
        $category = Category::with('subcategories')
            ->withCount('products')
            ->findOrFail($id);

        return new CategoryResource($category);
    }

    public function getUserOrders(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['data' => $orders]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'shipping_address' => 'nullable|array',
            'billing_address' => 'nullable|array',
            'shipping_cost' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.price' => 'nullable|numeric|min:0',
            'items.*.attributes' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($request) {
            // Calculate total from items (prefer unit_price, fall back to price)
            $subtotal = collect($request->items)->sum(function ($item) {
                $unitPrice = $item['unit_price'] ?? $item['price'] ?? 0;
                return $unitPrice * $item['quantity'];
            });

            $shippingCost = $request->shipping_cost ?? 0;
            $total = $subtotal + $shippingCost;

            // Generate unique order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => auth('sanctum')->id(),
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone ?? $request->phone,
                'phone' => $request->phone ?? $request->customer_phone,
                'address' => $request->address,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_price' => $total,
                'total' => $total,
                'currency_code' => $request->currency_code ?? 'SAR',
                'status' => 'pending',
                'payment_status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['unit_price'] ?? $item['price'] ?? 0,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully',
                'data' => $order->load('items'),
            ], 201);
        });
    }

    public function getOffers()
    {
        $offers = \App\Models\Offer::where('status', 1)
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })->get();

        return response()->json([
            'data' => $offers
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // max 2MB
        ]);

        $user = $request->user();

        // Handle profile image upload via Storage facade (works on Render/Vercel/local)
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                $oldPath = str_replace('storage/', '', $user->profile_image);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }

            // Store new image
            $image = $request->file('profile_image');
            $path = $image->store('profiles', 'public');
            $user->profile_image = 'storage/' . $path;
        }

        // Update other fields
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'profile_image' => $user->profile_image ? url($user->profile_image) : null,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    public function getWishlist(Request $request)
    {
        $wishlist = \App\Models\Wishlist::with('product.images')
            ->where('user_id', $request->user()->id)
            ->get()
            ->pluck('product');

        return ProductResource::collection($wishlist);
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        
        $exists = \App\Models\Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['message' => 'Removed from wishlist', 'added' => false]);
        }

        \App\Models\Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id
        ]);

        return response()->json(['message' => 'Added to wishlist', 'added' => true]);
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;

        // Update existing review or create new one
        $review = \App\Models\Review::updateOrCreate(
            ['user_id' => $userId, 'product_id' => $productId],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }
}
