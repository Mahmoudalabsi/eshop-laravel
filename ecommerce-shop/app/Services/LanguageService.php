<?php

namespace App\Services;

use App\Models\Language; // استيراد الموديل الجديد
use Illuminate\Support\Facades\Log;

class LanguageService
{
    /**
     * جلب جميع اللغات من قاعدة البيانات
     */
    public function getAll()
    {
        try {
            // جلب البيانات من الجدول مباشرة وترتيبها (الافتراضية أولاً)
            return Language::orderBy('is_default', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Database Error in LanguageService:getAll: ' . $e->getMessage());
            return collect([]); // إرجاع مجموعة فارغة في حال الخطأ
        }
    }

    /**
     * جلب لغة واحدة بواسطة المعرف
     */
    public function find($id)
    {
        try {
            return Language::find($id);
        } catch (\Exception $e) {
            // Error ignored
        }

        return null;
    }

    /**
     * البحث عن لغة بالكود
     */
    public function findByCode($code)
    {
        return Language::where('code', $code)->first();
    }

    /**
     * الحصول على اللغة الافتراضية
     */
    public function getDefault()
    {
        // استخدام الـ Scope الذي عرفته في الموديل سابقاً
        return Language::where('is_default', true)->first();
    }

    /**
     * الحصول على اللغات المفعلة فقط
     */
    public function getActive()
    {
        // استخدام الـ Scope Active
        return Language::where('status', true)->get();
    }
}
