<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $user = Session::get('user');

        // Fetch stats from API
        $orderResponse = $this->api->get('/orders');
        $orders = collect($orderResponse->get('data', []));

        $wishlistResponse = $this->api->get('/wishlist');
        $wishlistCount = count($wishlistResponse->get('data', []));

        $stats = [
            'total_orders' => $orders->count(),
            'wishlist_count' => $wishlistCount,
            'total_spent' => $orders->sum('total_price'),
            'member_since' => data_get($user, 'created_at')
                ? \Carbon\Carbon::parse(data_get($user, 'created_at'))->translatedFormat('F Y')
                : \Carbon\Carbon::parse(auth()->user()->created_at)->translatedFormat('F Y')
        ];

        $recentOrders = $orders->take(5)->map(fn($o) => (object) $o);

        return view('profile.index', compact('user', 'stats', 'recentOrders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // max 2MB
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image');
        }

        $response = $this->api->post('/profile/update', $data, true); // true for multipart

        if ($response->get('error')) {
            return back()->with('error', __('messages.profile_update_failed'));
        }

        // Update session user
        Session::put('user', $response->get('user'));

        return back()->with('success', __('messages.profile_updated_success'));
    }
}
