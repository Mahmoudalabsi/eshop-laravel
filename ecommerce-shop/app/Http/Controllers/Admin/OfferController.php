<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function index()
    {
        return view('eshop.dashboard.offers', [
            'offers' => Offer::all(),
            'categories' => Category::all(),
            'subcategories' => Subcategory::all(),
            'products' => Product::select('id', 'name')->get(),
        ]);
    }

public function updateStatus(Request $request, $id)
{
    return DB::transaction(function () use ($request, $id) {
        $offer = Offer::findOrFail($id);
        $now = now(); // الوقت الحالي

        // إذا حاول المستخدم التفعيل يدوياً ولكن الموعد لم يحن بعد أو انتهى
        if ($request->status == 1) {
            if ($now->lt($offer->starts_at)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لا يمكن تفعيل العرض قبل موعد بدئه: ' . $offer->starts_at
                ], 422);
            }
            if ($now->gt($offer->ends_at)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذا العرض منتهي الصلاحية ولا يمكن تفعيله.'
                ], 422);
            }
        }

        $offer->status = $request->status;
        $offer->save();

        // تحديد المنتجات بناءً على الـ target_id (الاسم الصحيح لجدولك)
        $targetId = $offer->target_id;
        $productQuery = Product::query();

        if ($offer->scope == 'category') {
            $productQuery->whereIn('subcategory_id', function($q) use ($targetId) {
                $q->select('id')->from('subcategories')->where('category_id', $targetId);
            });
        } elseif ($offer->scope == 'subcategory') {
            $productQuery->where('subcategory_id', $targetId);
        } elseif ($offer->scope == 'product') {
            $productQuery->where('id', $targetId);
        }

        $products = $productQuery->get();

        foreach ($products as $product) {
            if ($offer->status == 1) {
                // تفعيل الخصم وتحديث السعر الحالي
                $priceBeforeDiscount = $product->price;

                if ($offer->type == 'percentage') {
                    $newPrice = $priceBeforeDiscount - ($priceBeforeDiscount * ($offer->discount_value / 100));
                } else {
                    $newPrice = $priceBeforeDiscount - $offer->discount_value;
                }

                $product->update([
                    'old_price' => $priceBeforeDiscount,
                    'price' => max(0, $newPrice),
                    'offer_expires_at' => $offer->ends_at
                ]);
            } else {
                // إعادة السعر لأصله
                if ($product->old_price) {
                    $product->update([
                        'price' => $product->old_price,
                        'old_price' => null,
                        'offer_expires_at' => null
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success', 'message' => 'تم تحديث حالة العرض والأسعار بنجاح']);
    });
}
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'discount_value' => 'required|numeric',
        'type' => 'required|in:percentage,fixed',
        'scope' => 'required|in:all,category,subcategory,product',
        'starts_at' => 'required|date',
        'ends_at' => 'required|date|after:starts_at',
    ]);

    try {
        $offer = new Offer();
        $offer->name = $request->name;
        $offer->discount_value = $request->discount_value;
        $offer->type = $request->type;
        $offer->scope = $request->scope;


        $offer->target_id = ($request->scope == 'all') ? null : $request->target_id;

        $offer->starts_at = $request->starts_at;
        $offer->ends_at = $request->ends_at;
        $offer->status = 0;
        $offer->save();

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء العرض بنجاح بنجاح، يمكنك تفعيله الآن.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()
        ], 500);
    }
}

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);

        DB::beginTransaction();
        try {
            $targetId = $offer->o_target_id ?? $offer->target_id;

            $productQuery = Product::query();
            if ($offer->scope == 'category') {
                $productQuery->whereIn('subcategory_id', function($q) use ($targetId) {
                    $q->select('id')->from('subcategories')->where('category_id', $targetId);
                });
            } elseif ($offer->scope == 'subcategory') {
                $productQuery->where('subcategory_id', $targetId);
            } elseif ($offer->scope == 'product') {
                $productQuery->where('id', $targetId);
            }

            $productQuery->whereNotNull('old_price')->update([
                'price' => DB::raw('old_price'),
                'old_price' => null,
                'offer_expires_at' => null
            ]);

            $offer->delete();
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'تم حذف العرض واستعادة الأسعار الأصلية']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => 'فشل الحذف'], 500);
        }
    }
public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'discount_value' => 'required|numeric',
        'type' => 'required|in:percentage,fixed',
        'scope' => 'required|in:all,category,subcategory,product',
        'target_id' => 'required_unless:scope,all',
        'starts_at' => 'required|date',
        'ends_at' => 'required|date|after:starts_at',
    ]);

    $offer = Offer::findOrFail($id);

    // 2. منع التعديل إذا كان العرض نشطاً (منطق سليم جداً)
    if ($offer->status == 1) {
        return response()->json(['message' => 'يجب إيقاف العرض أولاً قبل تعديل بياناته'], 422);
    }

    $offer->update($request->only([
        'name',
        'discount_value',
        'type',
        'scope',
        'target_id',
        'starts_at',
        'ends_at'
    ]));

    if ($request->scope === 'all') {
        $offer->update(['target_id' => null]);
    }

    return response()->json(['success' => true, 'message' => 'تم تحديث بيانات العرض بنجاح']);
}
}
