<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $response = $this->api->get('/wishlist');
        $products = collect($response->get('data', []))->map(fn($item) => (object) $item);

        return view('wishlist.index', compact('products'));
    }

    public function toggle(Request $request)
    {
        if (!Session::has('api_token')) {
            return response()->json(['error' => __('messages.wishlist_login_required')], 401);
        }

        $response = $this->api->post('/wishlist/toggle', [
            'product_id' => $request->product_id
        ]);

        return response()->json($response->all());
    }
}
