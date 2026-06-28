<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory; // Added this import
use App\Models\Product;     // Added this import
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('eshop.dashboard.category');
    }

    public function getCategoriesJson()
    {
        $categories = Category::with(['sizeGuide'])->withCount(['subcategories', 'products'])->latest()->get();
        return response()->json(['data' => $categories]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => 1,
                'size_guide_id' => $request->size_guide_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إضافة القسم بنجاح!',
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل الحفظ: ' . $e->getMessage()
            ], 422);
        }
    }
// Added method to address a reported bug
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            // 2. Fetch the target record
            $category = Category::findOrFail($id);

            // 3. Perform the update
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'size_guide_id' => $request->size_guide_id,
            ]);

            // 4. Return success (handled by SweetAlert on frontend)
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث بيانات القسم بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحديث: ' . $e->getMessage()
            ], 500);
        }
    }
    // Cascade status update logic
    public function updateStatus(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $status = $request->status; // 1 or 0

            $category->status = $status;
            $category->save();

            // 1. Update child subcategories (direct relation)
            Subcategory::where('category_id', $id)->update(['status' => $status]);

            // 2. Update products via subcategory relation
            // Lookup products whose subcategory belongs to this category
            Product::whereHas('subcategory', function ($query) use ($id) {
                $query->where('category_id', $id);
            })->update(['status' => $status]);

            return response()->json([
                'status' => 'success',
                'message' => $status == 0 ? 'تم تعطيل القسم وتوابعه بنجاح' : 'تم تفعيل القسم وتوابعه بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحديث: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete(); // Will delete dependents if DB cascade is enabled
        return response()->json(['success' => 'تم حذف القسم بنجاح!']);
    }
    public function getSubcategoriesForCategory($id)
    {
        try {
            $subcategories = Subcategory::where('category_id', $id)
                ->withCount('products')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $subcategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب البيانات'
            ], 500);
        }
    }
}
