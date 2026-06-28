<?php

namespace App\Services;

use View;

class CurrencyService
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    /**
     * جلب جميع العملات
     */
    public function getAll()
    {
        try {
            $response = $this->api->get('/currencies');
            $data = $response->get('data') ?? [];

            return collect($data)->map(function($item) {
                return (object) $item;
            });
        } catch (\Exception $e) {
            // إرجاع عملات افتراضية في حالة الفشل
            return collect([
                (object) ['id' => 1, 'name' => 'ريال سعودي', 'code' => 'SAR', 'symbol' => 'ر.س', 'exchange_rate' => 1.0, 'is_default' => true, 'status' => true],
                (object) ['id' => 2, 'name' => 'دولار أمريكي', 'code' => 'USD', 'symbol' => '$', 'exchange_rate' => 3.75, 'is_default' => false, 'status' => true],
                (object) ['id' => 3, 'name' => 'يورو', 'code' => 'EUR', 'symbol' => '€', 'exchange_rate' => 4.10, 'is_default' => false, 'status' => true],
            ]);
        }
    }

    /**
     * جلب عملة بالكود
     */
    public function findByCode($code)
    {
        $currencies = $this->getAll();
        return $currencies->where('code', $code)->first();
    }

    /**
     * الحصول على العملة الافتراضية
     */
    public function getDefault()
    {
        $currencies = $this->getAll();
        return $currencies->where('is_default', true)->first();
    }

    /**
     * الحصول على العملات المفعلة فقط
     */
    public function getActive()
    {
        $currencies = $this->getAll();
        return $currencies->where('status', true);
    }
}
