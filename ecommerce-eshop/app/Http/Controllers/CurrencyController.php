<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function setCurrency($code) {
        $currency = Currency::where('code', $code)->first();
        
        // Fallback if DB is empty
        if (!$currency) {
            $defaults = [
                'SAR' => ['symbol' => 'ر.س', 'rate' => 1.0],
                'USD' => ['symbol' => '$', 'rate' => 0.27],
                'EGP' => ['symbol' => 'ج.م', 'rate' => 12.0], // Approx rate
            ];
            
            if (isset($defaults[$code])) {
                $currency = (object) [
                    'code' => $code,
                    'symbol' => $defaults[$code]['symbol'],
                    'exchange_rate' => $defaults[$code]['rate']
                ];
            }
        }

        if ($currency) {
            session([
                'currency' => $code,
                'currency_symbol' => $currency->symbol,
                'currency_rate' => $currency->exchange_rate
            ]);
            session()->save(); // Force save
        }
        return redirect()->back()->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
