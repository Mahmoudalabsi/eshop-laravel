<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory; // أضفنا الموديل
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // إحصائيات مخزنة مؤقتاً لمدة 1 ساعة
        $categoriesCount = cache()->remember('stats.categories.count', 3600, function () {
            return Category::count();
        });

        $subCategoriesCount = cache()->remember('stats.subcategories.count', 3600, function () {
            return Subcategory::count();
        });

        $productsCount = cache()->remember('stats.products.count', 3600, function () {
            return Product::count();
        });

        $usersCount = cache()->remember('stats.users.count', 3600, function () {
            return User::count();
        });

        $ordersCount = cache()->remember('stats.orders.count', 3600, function () {
            return Order::count();
        });

        // جلب آخر 5 طلبات فقط مع اسم المستخدم
        $latestOrders = Order::with('user:id,name')
            ->select('id', 'user_id', 'status', 'total_price', 'total', 'created_at')
            ->latest()
            ->take(5)
            ->get();

        // بيانات الأقسام مع عدد المنتجات
        $categoriesWithProducts = cache()->remember('stats.categories.products', 3600, function () {
            return Category::select('id', 'name')
                ->withCount('products')
                ->has('products')
                ->get();
        });

        $categoryData = [
            'labels' => $categoriesWithProducts->pluck('name'),
            'counts' => $categoriesWithProducts->pluck('products_count')
        ];

        // بيانات المبيعات - ذات أولوية أقل
        $salesLabels = [];
        $salesCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $currentDate = now()->subDays($i);
            $dateString = $currentDate->format('Y-m-d');
            $salesLabels[] = $currentDate->translatedFormat('l');
            $salesCounts[] = 0; // عرض 0 في البداية، سيتم تحميل البيانات الفعلية عبر AJAX
        }

        return view('eshop.dashboard.index', compact(
            'categoriesCount',
            'subCategoriesCount',
            'productsCount',
            'usersCount',
            'ordersCount',
            'latestOrders',
            'categoryData',
            'salesLabels',
            'salesCounts'
        ));
    }

    /**
     * Get sales data asynchronously (called via AJAX)
     */
    public function getSalesData()
    {
        $salesData = cache()->remember('stats.sales.weekly', 600, function () {
            return Order::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(6))
                ->groupBy('date')
                ->get()
                ->keyBy('date');
        });

        $salesCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateString = now()->subDays($i)->format('Y-m-d');
            $dayData = $salesData->get($dateString);
            $salesCounts[] = $dayData ? $dayData->count : 0;
        }

        return response()->json(['salesCounts' => $salesCounts]);
    }
}
