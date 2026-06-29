<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Offer;
use App\Models\Review;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'       => User::count(),
            'customers'   => User::where('role', 'user')->count(),
            'admins'      => User::where('role', 'admin')->count(),
            'products'    => Product::count(),
            'categories'  => Category::count(),
            'orders'      => Order::count(),
            'offers'      => Offer::where('status', 1)->count(),
            'reviews'     => Review::count(),
            'currencies'  => Currency::count(),
            'languages'   => Language::count(),
        ];

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentProducts = Product::with('subcategory.category')->latest()->take(5)->get();
        $lowStockProducts = Product::where('total_stock', '<', 10)->orderBy('total_stock')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentProducts', 'lowStockProducts'));
    }
}
