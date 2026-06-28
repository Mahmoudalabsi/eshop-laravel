<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocalization
{
    public function handle(Request $request, Closure $next)
    {
        $lang = Session::get('locale', Session::get('language', config('app.locale')));
        App::setLocale($lang);

        if (!Session::has('currency')) {
            $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
            Session::put('currency', $defaultCurrency ? $defaultCurrency->code : 'SAR');
        }

        return $next($request);
    }
}
