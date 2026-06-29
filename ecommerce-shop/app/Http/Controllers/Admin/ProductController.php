<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\ProductImage; // الموديل الجديد
use App\Models\ProductAttribute; // الموديل الجديد
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all(); // لجلبها في نافذة الإضافة
        return view('eshop.dashboard.products', compact('categories', 'subcategories'));
    }

    // جلب البيانات مع العلاقات كاملة (رئيسي + فرعي)
    public function productsJson()
    {
        // تحميل كافة العلاقات المطلوبة لظهور البيانات في المودال
        $products = Product::with(['subcategory.category', 'attributes', 'images'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->get();

        return response()->json([
            'data' => $products
        ]);
    }
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            // التحقق من مصفوفة الخيارات الجديدة
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string',
            'variants.*.size' => 'required|string',
            'variants.*.qty' => 'required|integer|min:0',
        ]);

        return DB::transaction(function () use ($request) {

            // 2. حساب إجمالي المخزون من مصفوفة الخيارات
            $variants = $request->input('variants', []);
            $totalStock = collect($variants)->sum('qty');

            // 3. رفع الصورة الرئيسية
            $mainImagePath = $request->file('image')->store('products', 'public');

            // 3b. حساب نسبة الخصم إذا توفر السعر القديم
            $oldPrice = $request->filled('old_price') ? (float) $request->old_price : null;
            $discountPct = 0;
            $isOnOffer = false;
            if ($oldPrice && $oldPrice > (float) $request->price) {
                $discountPct = (int) round((($oldPrice - (float) $request->price) / $oldPrice) * 100);
                $isOnOffer = true;
            }

            // 4. إنشاء المنتج — تعيين كل الحقول المطلوبة حتى لا تكون NULL
            $name = $request->name;
            $product = Product::create([
                'name'                 => $name,
                'slug'                 => \Illuminate\Support\Str::slug($name . '-' . substr(md5($name . microtime()), 0, 6)),
                'short_description'    => $request->filled('short_description') ? $request->short_description : mb_substr($request->description ?? $name, 0, 150),
                'description'          => $request->description,
                'price'                => $request->price,
                'old_price'            => $oldPrice,
                'sku'                  => strtoupper(substr(md5($name . microtime()), 0, 8)),
                'subcategory_id'       => $request->subcategory_id,
                'image'                => $mainImagePath,
                'total_stock'          => $totalStock,
                'status'               => 1,
                'is_featured'          => $request->boolean('is_featured') ? 1 : 0,
                'is_on_offer'          => $isOnOffer ? 1 : 0,
                'discount_percentage'  => $discountPct,
            ]);

            // 5. حفظ الخيارات (اللون، المقاس، الكمية)
            foreach ($variants as $variant) {
                $product->attributes()->create([
                    'color' => $variant['color'],
                    'size' => $variant['size'],
                    'qty' => $variant['qty']
                ]);
            }

            // 6. الصور الإضافية (المعرض)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            return response()->json(['status' => 'success', 'message' => 'تم إضافة المنتج بخياراته بنجاح']);
        });
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'subcategory_id' => 'required|exists:subcategories,id',
            'variants' => 'nullable|array', // التأكد من وصول المقاسات كمصفوفة
        ]);

        return DB::transaction(function () use ($request, $product) {
            $data = $request->only(['name', 'description', 'price', 'subcategory_id', 'short_description']);

            // تحديث الـ slug إذا تغير الاسم
            if ($request->name !== $product->name) {
                $data['slug'] = \Illuminate\Support\Str::slug($request->name . '-' . substr(md5($request->name . microtime()), 0, 6));
            }

            // منطق السعر القديم + نسبة الخصم
            $oldPrice = $request->filled('old_price') ? (float) $request->old_price : null;
            if ($oldPrice !== null) {
                $data['old_price'] = $oldPrice;
                if ($oldPrice > (float) $request->price) {
                    $data['discount_percentage'] = (int) round((($oldPrice - (float) $request->price) / $oldPrice) * 100);
                    $data['is_on_offer'] = 1;
                } else {
                    $data['discount_percentage'] = 0;
                    $data['is_on_offer'] = 0;
                }
            }

            // علم "مميز"
            $data['is_featured'] = $request->boolean('is_featured') ? 1 : 0;

            // معالجة الصورة الرئيسية
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // --- التحديث الجديد للمقاسات (Variants) ---
            if ($request->has('variants')) {
                $product->attributes()->delete(); // حذف القديم
                $totalStock = 0;

                foreach ($request->variants as $variant) {
                    // التأكد من وجود مقاس على الأقل لإضافته
                    if (!empty($variant['size'])) {
                        $qty = $variant['qty'] ?? 0;
                        $totalStock += $qty;

                        $product->attributes()->create([
                            'color' => $variant['color'] ?? null,
                            'size' => $variant['size'],
                            'qty' => $qty
                        ]);
                    }
                }
                $data['total_stock'] = $totalStock;
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create(['image_path' => $path]);
                }
            }

            $product->update($data);
            return response()->json(['status' => 'success', 'message' => 'تم تحديث المنتج بنجاح']);
        });
    }
    // public function productsJson()
    // {
    //     // أضفنا شرط whereHas لفلترة المنتجات بناءً على حالة القسم التابع لها
    //     $products = Product::with(['category', 'attributes', 'images'])
    //         ->whereHas('category', function ($query) {
    //             $query->where('status', 1);
    //         })
    //         ->get();
    //     $products = Product::withoutGlobalScope('activeCategory')->get();
    //     return response()->json([
    //         'data' => $products
    //     ]);
    // }

    // يييييي

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // حذف الصورة الرئيسية والفرعية من التخزين
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $product->delete(); // سيحذف التقييمات والأحجام تلقائياً بسبب onDelete('cascade')

        return response()->json(['status' => 'success', 'message' => 'تم حذف المنتج وكافة ملحقاته']);
    }

    public function deleteReview($id)
    {
        Review::destroy($id);
        return response()->json(['success' => true]);
    }
    public function deleteImage(Request $request)
    {
        $product = Product::findOrFail($request->id);

        if ($request->type === 'main') {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
                $product->image = null;
                $product->save();
            }
        } else {
            // تنظيف المسار المرسل: إذا كان يحتوي على /storage/ نقوم بإزالتها
            $cleanPath = str_replace('/storage/', '', $request->path);

            $imageRecord = ProductImage::where('image_path', $cleanPath)
                ->where('product_id', $request->id)
                ->first();

            if ($imageRecord) {
                // حذف الملف الفيزيائي
                Storage::disk('public')->delete($imageRecord->image_path);
                // حذف السجل من قاعدة البيانات
                $imageRecord->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم الحذف بنجاح'
        ]);
    }
    public function toggleStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // لا نسمح بالتفعيل (1) إذا كان القسم معطلاً، ولكن نسمح بالإلغاء (0) دائماً
        if ($request->status == 1 && $product->subcategory->status == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكن تفعيل المنتج لأن القسم التابع له معطل!'
            ], 422);
        }
        $product->status = $request->status;
        $product->save();

        return response()->json(['success' => true]);
    }
    public function getReviews($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'المنتج غير موجود'], 404);
        }

        $reviews = Review::with('user')
            ->where('product_id', $id)
            ->get();

        return response()->json($reviews); // هذا سيضمن إرسال JSON صحيح
    }
}