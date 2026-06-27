<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('eshop.dashboard.subcategory', compact('categories'));
    }

    public function getSubcategoriesJson()
    {
        // جلب الأقسام الفرعية مع القسم الرئيسي التابع لها
        $subs = Subcategory::with('category')->withCount('products')->latest()->get();
        return response()->json(['data' => $subs]);
    }

    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // التأكد من وجود القسم الرئيسي
        ]);

        // 2. الإنشاء
        $subcategory = Subcategory::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'status' => 1,
        ]);

        // 3. الرد (ضروري جداً لنجاح الجافا سكريبت)
        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة القسم الفرعي بنجاح',
            'data' => $subcategory
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $sub = Subcategory::findOrFail($id);
            $status = $request->status; // القيمة 0 أو 1

            // 1. تحديث حالة القسم الفرعي
            $sub->status = $status;
            $sub->save();

            // 2. تحديث المنتجات (حتى لو كانت آلاف المنتجات، هذا الاستعلام سريع جداً)
            // سيتم تحويل حالة جميع المنتجات التابعة لتطابق حالة القسم
            $sub->products()->update(['status' => $status]);

            return response()->json([
                'status' => 'success',
                'message' => $status == 1 ? 'تم تفعيل القسم وجميع منتجاته' : 'تم تعطيل القسم وجميع منتجاته'
            ]);

        } catch (\Exception $e) {
            // إرسال الخطأ الحقيقي للمساعدة في التصحيح
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحديث: ' . $e->getMessage()
            ], 500);
        }
    }
    // أضف هذه الدوال داخل الكنترولر
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id'
        ]);

        $sub = Subcategory::findOrFail($id);
        $sub->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
        ]);

        return response()->json(['message' => 'تم التحديث بنجاح']);
    }

    public function destroy($id)
    {
        $sub = Subcategory::findOrFail($id);
        $sub->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
    public function getProducts($id)
    {
        $products = Product::where('subcategory_id', $id)->get();
        return response()->json(['data' => $products]);
    }
}
