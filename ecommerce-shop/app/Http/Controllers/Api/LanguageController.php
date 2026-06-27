<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * جلب جميع اللغات
     */
    public function index()
    {
        $languages = Language::all();
        return response()->json([
            'data' => $languages
        ]);
    }

    /**
     * جلب اللغات المفعلة فقط
     */
    public function getActive()
    {
        $languages = Language::where('status', true)->get();
        return response()->json([
            'data' => $languages
        ]);
    }

    /**
     * جلب لغة واحدة
     */
    public function show($id)
    {
        try {
            $language = Language::findOrFail($id);
            return response()->json(['data' => $language]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'اللغة غير موجودة'], 404);
        }
    }

    /**
     * إضافة لغة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:languages,code',
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'nullable|in:0,1',
        ]);
        // Convert is_default to boolean
        $validated['is_default'] = (bool) ($validated['is_default'] ?? false);

        try {
            // إذا كانت اللغة الجديدة افتراضية، قم بإزالة الافتراضي من اللغات الأخرى
            if ($validated['is_default'] ?? false) {
                Language::update(['is_default' => false]);
            }

            $language = Language::create($validated);
            return response()->json(['data' => $language, 'message' => 'تمت إضافة اللغة بنجاح'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * تحديث لغة
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:languages,code,' . $id,
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'nullable|in:0,1',
        ]);
        // Convert is_default to boolean
        $validated['is_default'] = (bool) ($validated['is_default'] ?? false);

        try {
            $language = Language::findOrFail($id);

            // إذا كانت اللغة المحدثة افتراضية، قم بإزالة الافتراضي من اللغات الأخرى
            if ($validated['is_default'] ?? false) {
                Language::where('id', '!=', $id)->update(['is_default' => false]);
            }

            $language->update($validated);
            return response()->json(['data' => $language, 'message' => 'تم تحديث اللغة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * تبديل حالة اللغة
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $language = Language::findOrFail($id);
            $language->status = $request->get('status', !$language->status);
            $language->save();

            return response()->json(['data' => $language, 'message' => 'تم تحديث الحالة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * حذف لغة
     */
    public function destroy($id)
    {
        try {
            $language = Language::findOrFail($id);

            // منع حذف اللغة الافتراضية
            if ($language->is_default) {
                return response()->json(['error' => 'لا يمكن حذف اللغة الافتراضية'], 422);
            }

            $language->delete();
            return response()->json(['message' => 'تم حذف اللغة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
