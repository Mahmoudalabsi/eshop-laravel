<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Get all products with filtering, sorting, and pagination.
     * Uses local Eloquent models (no external API calls).
     */
    public function getAll(array $params = [])
    {
        $query = Product::query()
            ->with(['subcategory.category', 'images', 'reviews'])
            ->active();

        // Search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if (!empty($params['category_id'])) {
            $query->whereHas('subcategory', function ($q) use ($params) {
                $q->where('category_id', $params['category_id']);
            });
        }

        // Subcategory filter
        if (!empty($params['subcategory_id'])) {
            $query->where('subcategory_id', $params['subcategory_id']);
        }

        // Featured
        if (!empty($params['featured'])) {
            $query->where('is_featured', true);
        }

        // Offers
        if (!empty($params['offers'])) {
            $query->where('is_on_offer', true);
        }

        // Max price
        if (!empty($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }

        // Color filter
        if (!empty($params['color'])) {
            $query->whereHas('attributes', function ($q) use ($params) {
                $q->where('color', $params['color']);
            });
        }

        // Size filter
        if (!empty($params['size'])) {
            $query->whereHas('attributes', function ($q) use ($params) {
                $q->where('size', $params['size']);
            });
        }

        // Sorting
        $sort = $params['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'top_rated':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $perPage = (int) ($params['per_page'] ?? 12);
        $page = (int) ($params['page'] ?? request()->input('page', 1));

        $total = $query->count();
        $items = $query->forPage($page, $perPage)->get();

        // Map to objects with additional fields for Blade compatibility
        $items = $items->map(function ($p) {
            return $this->enrichProduct($p);
        });

        return collect([
            'items' => $items,
            'meta' => (object) [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    /**
     * Find a single product by ID with relationships.
     */
    public function find($id)
    {
        $product = Product::with(['subcategory.category', 'images', 'reviews.user', 'attributes'])
            ->active()
            ->find($id);

        if (!$product) {
            return null;
        }

        return $this->enrichProduct($product);
    }

    public function getFeatured($limit = 8)
    {
        $items = Product::with(['subcategory.category', 'images'])
            ->active()
            ->featured()
            ->inStock()
            ->take($limit)
            ->get();

        return $items->map(fn($p) => $this->enrichProduct($p));
    }

    public function getOffers($limit = 12)
    {
        $items = Product::with(['subcategory.category', 'images'])
            ->active()
            ->onOffer()
            ->take($limit)
            ->get();

        return $items->map(fn($p) => $this->enrichProduct($p));
    }

    public function getOnOffer($limit = 12)
    {
        return $this->getOffers($limit);
    }

    public function getTopRated($limit = 4)
    {
        $items = Product::with(['subcategory.category', 'images', 'reviews'])
            ->active()
            ->inStock()
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take($limit)
            ->get();

        return $items->map(fn($p) => $this->enrichProduct($p));
    }

    public function getRelated($productId, $limit = 5)
    {
        $product = Product::find($productId);
        if (!$product) {
            return collect([]);
        }

        $items = Product::with(['subcategory.category', 'images'])
            ->active()
            ->where('id', '!=', $productId)
            ->where('subcategory_id', $product->subcategory_id)
            ->inStock()
            ->take($limit)
            ->get();

        return $items->map(fn($p) => $this->enrichProduct($p));
    }

    public function search($query, $limit = 20)
    {
        $items = Product::with(['subcategory.category', 'images'])
            ->active()
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->take($limit)
            ->get();

        return $items->map(fn($p) => $this->enrichProduct($p));
    }

    public function submitReview(array $data)
    {
        try {
            \App\Models\Review::create([
                'user_id'    => auth()->id(),
                'product_id' => $data['product_id'],
                'rating'     => $data['rating'],
                'comment'    => $data['comment'] ?? null,
            ]);
            return collect(['status' => 'success']);
        } catch (\Exception $e) {
            return collect(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Return available filters for the shop sidebar.
     */
    public function getFilters()
    {
        $categories = Category::withCount('products')->active()->get();

        $colors = \App\Models\ProductAttribute::select('color')
            ->distinct()
            ->whereNotNull('color')
            ->pluck('color')
            ->toArray();

        $sizes = \App\Models\ProductAttribute::select('size')
            ->distinct()
            ->whereNotNull('size')
            ->pluck('size')
            ->toArray();

        $maxPrice = (float) Product::active()->max('price');

        return [
            'categories' => $categories,
            'colors' => $colors,
            'sizes' => $sizes,
            'max_price' => $maxPrice,
        ];
    }

    /**
     * Convert a Product model into an enriched stdClass for Blade compatibility
     * with API-driven legacy views.
     */
    protected function enrichProduct($p)
    {
        $obj = (object) $p->toArray();
        $obj->id = $p->id;
        $obj->name = $p->name;
        $obj->slug = $p->slug ?? null;
        $obj->description = $p->description;
        $obj->short_description = $p->short_description ?? null;
        $obj->price = (float) $p->price;
        $obj->old_price = $p->old_price ? (float) $p->old_price : null;
        $obj->image = $p->image;
        $obj->image_url = $p->image;
        $obj->is_featured = (bool) $p->is_featured;
        $obj->is_on_offer = (bool) $p->is_on_offer;
        $obj->discount_percentage = $p->discount_percentage ?? 0;
        $obj->discounted_price = $p->discounted_price;
        $obj->price_formatted = number_format($p->price, 2);
        $obj->stock = (int) ($p->total_stock ?? 0);
        $obj->stock_status = (object) [
            'available' => ($p->total_stock ?? 0) > 0,
            'total_qty' => (int) ($p->total_stock ?? 0),
        ];
        $obj->subcategory = $p->subcategory ? (object) [
            'id' => $p->subcategory->id,
            'name' => $p->subcategory->name,
            'category' => $p->subcategory->category ? (object) [
                'id' => $p->subcategory->category->id,
                'name' => $p->subcategory->category->name,
            ] : null,
        ] : null;
        $obj->reviews = $p->reviews ?? collect([]);
        $obj->images = $p->images ?? collect([]);
        $obj->average_rating = $p->reviews_avg_rating ?? ($p->reviews ? $p->reviews->avg('rating') : 0);
        $obj->reviews_count = $p->reviews_count ?? ($p->reviews ? $p->reviews->count() : 0);
        return $obj;
    }
}
