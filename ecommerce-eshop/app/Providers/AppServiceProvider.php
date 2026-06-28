<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Blade;
use App\Services\CurrencyService;
use App\Services\LanguageService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Lazy load common data for all views
        View::composer('*', function ($view) {
            try {
                $currencies = $this->getCurrenciesLazy();
                $languages = $this->getLanguagesLazy();
                $selectedCurrencyCode = Session::get('currency', 'SAR');
                $selectedCurrency = collect($currencies)->where('code', $selectedCurrencyCode)->first()
                    ?? collect($currencies)->where('is_default', true)->first()
                    ?? (object)['code' => 'SAR', 'symbol' => 'ر.س', 'exchange_rate' => 1.0, 'name' => 'ريال سعودي'];

                if (is_array($selectedCurrency)) {
                    $selectedCurrency = (object) $selectedCurrency;
                }

                $selectedLanguageCode = Session::get('locale', Session::get('language', config('app.locale')));
                $selectedLanguage = collect($languages)->where('code', $selectedLanguageCode)->first()
                    ?? collect($languages)->where('is_default', true)->first()
                    ?? (object)['code' => 'ar', 'name' => 'العربية', 'flag' => '🇸🇦', 'direction' => 'rtl'];

                if (is_array($selectedLanguage)) {
                    $selectedLanguage = (object) $selectedLanguage;
                }

                if ($selectedLanguageCode) {
                    app()->setLocale($selectedLanguageCode);
                }

                $view->with([
                    'currencies'        => $currencies,
                    'selectedCurrency'  => $selectedCurrency,
                    'languages'         => $languages,
                    'selectedLanguage'  => $selectedLanguage,
                    'currentLocale'     => $selectedLanguageCode,
                ]);
            } catch (\Exception $e) {
                // Error ignored - fallback values used
            }
        });

        // Custom @price directive
        Blade::directive('price', function ($expression) {
            return "<?php
                \$rate = session('currency_rate', 1);
                \$symbol = session('currency_symbol', 'ر.س');
                echo number_format($expression * \$rate, 2) . ' ' . \$symbol;
            ?>";
        });
    }

    private function getCurrenciesLazy()
    {
        try {
            return Cache::remember('shop_currencies', 3600, function() {
                $service = app(CurrencyService::class);
                $data = $service->getAll();
                return collect($data)->map(fn($item) => is_object($item) ? $item : (object) $item)->all();
            });
        } catch (\Exception $e) {
            return [(object)['code' => 'SAR', 'symbol' => 'ر.س', 'exchange_rate' => 1.0, 'name' => 'ريال سعودي', 'is_default' => true]];
        }
    }

    private function getLanguagesLazy()
    {
        try {
            return Cache::remember('shop_languages', 3600, function() {
                $service = app(LanguageService::class);
                $data = $service->getActive();
                return collect($data)->map(fn($item) => is_object($item) ? $item : (object) $item)->all();
            });
        } catch (\Exception $e) {
            return [
                (object)['code' => 'ar', 'name' => 'العربية', 'flag' => '🇸🇦', 'direction' => 'rtl', 'is_default' => true],
                (object)['code' => 'en', 'name' => 'English', 'flag' => '🇺🇸', 'direction' => 'ltr'],
            ];
        }
    }
}
