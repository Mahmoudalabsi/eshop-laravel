<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OfferController extends Controller
{
    protected $productService;
    protected $api;

    public function __construct(ProductService $productService, ApiService $api)
    {
        $this->productService = $productService;
        $this->api = $api;
    }

    public function index(Request $request)
    {
        // 1. Fetch Products on offer
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

        // 2. Fetch Active Campaigns (Promotion Banners)
        $campaigns = collect();
        try {
            $campResponse = $this->api->get('/offers');
            $campData = $campResponse->get('data') ?? $campResponse->all();
            $campaigns = collect($campData)->filter(fn($item) => data_get($item, 'status') == 1);
        } catch (\Exception $e) {
            // Fail silently
        }

        return view('offers.index', compact('products', 'campaigns'));
    }

    public function apiIndex()
    {
        try {
            // Fetch real offers from the control panel API (/offers endpoint)
            $response = $this->api->get('/offers');
            $data = $response->get('data') ?? $response->all();
            
            // Filter only active offers (status = 1) if possible
            $offers = collect($data)
                ->filter(fn($item) => data_get($item, 'status') == 1)
                ->map(function ($item) {
                    $title = data_get($item, 'name');
                    $value = data_get($item, 'discount_value');
                    
                    $discountStr = '';
                    if ($value) {
                        $discountStr = " (" . $value . ")";
                    }
                    
                    return "🔥 $title $discountStr";
                })->toArray();

        } catch (\Exception $e) {
            $offers = [];
        }

        // Fallback if no specific offers found from the API
        if (empty($offers)) {
            $offers = [
                __('messages.marketing_offer') !== 'messages.marketing_offer' ? __('messages.marketing_offer') : "✨ عروض حصرية: خصومات تصل إلى 50% على تشكيلة الموسم الجديد! تسوقي الآن ✨",
                __('messages.free_shipping_over') !== 'messages.free_shipping_over' ? "🚚 " . __('messages.free_shipping_over') : "🚚 شحن مجاني للطلبات فوق 500 ريال"
            ];
        }

        return response()->json([
            'status' => true,
            'data' => array_values($offers)
        ]);
    }
}
