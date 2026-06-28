<?php

if (!function_exists('calculate_discount')) {
    /**
     * حساب نسبة الخصم بين السعر القديم والجديد
     */
    function calculate_discount($price, $oldPrice)
    {
        if ($oldPrice > 0 && $oldPrice > $price) {
            $discount = (($oldPrice - $price) / $oldPrice) * 100;
            return round($discount) . '%';
        }
        return '0%';
    }
}

if (!function_exists('get_stock_badge')) {
    /**
     * إرجاع كلاس CSS بناءً على حالة المخزون
     */
    function get_stock_badge($qty)
    {
        if ($qty <= 0)
            return 'bg-danger'; // نفذت الكمية
        if ($qty <= 5)
            return 'bg-warning'; // كمية محدودة
        return 'bg-success'; // متوفر
    }
}