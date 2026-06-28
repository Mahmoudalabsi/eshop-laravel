<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language; // التعامل مع الموديل مباشرة
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    // لم نعد بحاجة لـ ApiService هنا
    public function __construct()
    {
    }

    public function index()
    {
        // جلب اللغات من الداتابيز مباشرة
        $languages = Language::orderBy('is_default', 'desc')->get();
        return view('eshop.dashboard.language', compact('languages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code',
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'nullable',
        ]);

        $isDefault = $request->has('is_default') || $request->is_default == 1;

        // إذا تم اختيارها كافتراضية، نجعل اللغات الأخرى غير افتراضية
        if ($isDefault) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        try {
            $language = Language::create([
                'name'      => $validated['name'],
                'code'      => strtolower($validated['code']),
                'flag'      => $validated['flag'],
                'direction' => $validated['direction'],
                'is_default'=> $isDefault,
                'status'    => true,
            ]);

            return response()->json(['message' => 'تمت إضافة اللغة بنجاح', 'data' => $language]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:languages,code,' . $id,
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'nullable',
        ]);

        $isDefault = $request->has('is_default') || $request->is_default == 1;

        if ($isDefault) {
            Language::where('id', '!=', $id)->update(['is_default' => false]);
        }

        $language->update([
            'name'      => $validated['name'],
            'code'      => strtolower($validated['code']),
            'flag'      => $validated['flag'],
            'direction' => $validated['direction'],
            'is_default'=> $isDefault,
        ]);

        return response()->json(['message' => 'تم تحديث اللغة بنجاح']);
    }

    public function updateStatus(Request $request, $id)
    {
        $language = Language::findOrFail($id);

        if ($language->is_default && !$request->status) {
            return response()->json(['message' => 'لا يمكن تعطيل اللغة الافتراضية'], 422);
        }

        $language->update(['status' => (bool)$request->status]);
        return response()->json(['message' => 'تم تحديث الحالة بنجاح']);
    }

    public function destroy($id)
    {
        $language = Language::findOrFail($id);

        if ($language->is_default) {
            return response()->json(['message' => 'لا يمكن حذف اللغة الافتراضية'], 422);
        }

        $language->delete();
        return response()->json(['message' => 'تم حذف اللغة بنجاح']);
    }

    public function setLanguage($code)
    {
        if (Language::where('code', $code)->where('status', true)->exists()) {
            session(['language' => $code]);
        }
        return back();
    }
}
