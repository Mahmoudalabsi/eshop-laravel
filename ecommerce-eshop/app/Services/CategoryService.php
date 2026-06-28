<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Category;

class CategoryService
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * جلب جميع الأقسام وتحويلها لكائنات
     */
    public function getAll()
    {
        try {
            $response = $this->api->get('/categories');
            $data = $response->get('data') ?? $response->all();

            // إذا لم تكن البيانات مصفوفة قائمة، نعتبرها فارغة
            if (!is_array($data) || !array_is_list($data)) {
                $data = [];
            }
        } catch (\Exception $e) {
            $data = [];
        }

        // تحويل البيانات المستخرجة إلى Collection من الكائنات
        $collection = collect($data)->map(fn($item) => (object) $item);

        // إذا كان الـ API فارغاً، نسحب من قاعدة البيانات المحلية (Fallback)
        if ($collection->isEmpty()) {
            return Category::all()->map(fn($c) => (object) $c->toArray());
        }

        return $collection;
    }

    /**
     * جلب قسم واحد بواسطة المعرف
     */
    public function find($id)
    {
        try {
            $response = $this->api->get("/categories/$id");
            $data = $response->get('data') ?? $response->all();

            if ($data) {
                return (object) $data;
            }
        } catch (\Exception $e) {
            // Error ignored
        }

        // البحث المحلي في حال فشل الـ API
        $localCategory = Category::find($id);

        // تحويل الموديل المحلي إلى كائن يدوي لضمان توافق Blade
        return $localCategory ? (object) $localCategory->toArray() : null;
    }
}
