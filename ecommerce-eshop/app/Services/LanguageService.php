<?php

namespace App\Services;

class LanguageService
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * جلب جميع اللغات
     */
    public function getAll()
    {
        try {
            $response = $this->api->get('/languages');
            $data = $response->get('data') ?? $response->all();

            if (!is_array($data) || !array_is_list($data)) {
                $data = [];
            }
        } catch (\Exception $e) {
            $data = [];
        }

        return collect($data)->map(fn($item) => (object) $item);
    }

    /**
     * جلب لغة واحدة بواسطة المعرف
     */
    public function find($id)
    {
        try {
            $response = $this->api->get("/languages/$id");
            $data = $response->get('data') ?? $response->all();

            if ($data) {
                return (object) $data;
            }
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
        $languages = $this->getAll();
        return $languages->where('code', $code)->first();
    }

    /**
     * الحصول على اللغة الافتراضية
     */
    public function getDefault()
    {
        $languages = $this->getAll();
        return $languages->where('is_default', true)->first();
    }

    /**
     * الحصول على اللغات المفعلة فقط
     */
    public function getActive()
    {
        $languages = $this->getAll();
        return $languages->where('status', true);
    }
}
