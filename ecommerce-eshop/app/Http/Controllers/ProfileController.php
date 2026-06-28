<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->latest()->get();
        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        $stats = [
            'total_orders'   => $orders->count(),
            'wishlist_count' => $wishlistCount,
            'total_spent'    => $orders->where('payment_status', 'paid')->sum('total'),
            'member_since'   => \Carbon\Carbon::parse($user->created_at)->translatedFormat('F Y'),
        ];

        $recentOrders = $orders->take(5);

        return view('profile.index', compact('user', 'stats', 'recentOrders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        $user = User::find(auth()->id());
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return back()->with('success', __('messages.profile_updated_success'));
    }
}
