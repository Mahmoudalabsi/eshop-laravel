<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OfferController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $response = $this->productService->getAll(['per_page' => 12, 'offers' => 1]);
        $items = $response->get('items');
        $meta = $response->get('meta');

        $products = new LengthAwarePaginator(
            $items,
            isset($meta->total) ? $meta->total : count($items),
            isset($meta->per_page) ? $meta->per_page : 12,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $campaigns = Offer::where('status', 1)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->get();

        return view('offers.index', compact('products', 'campaigns'));
    }

    public function apiIndex()
    {
        $offers = Offer::where('status', 1)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->get()
            ->map(function ($item) {
                $title = $item->name;
                $value = $item->discount_value;
                $discountStr = $value ? " ({$value}%)" : '';
                return "🔥 {$title}{$discountStr}";
            })
            ->toArray();

        if (empty($offers)) {
            $offers = [
                "✨ عروض حصرية: خصومات تصل إلى 50% على تشكيلة الموسم الجديد! تسوقي الآن ✨",
                "🚚 شحن مجاني للطلبات فوق 500 ريال"
            ];
        }

        return response()->json([
            'status' => true,
            'data' => array_values($offers)
        ]);
    }
}
