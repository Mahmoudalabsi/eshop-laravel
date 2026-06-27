<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\LanguageService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share languages only in layout views to avoid API loops
        \Illuminate\Support\Facades\View::composer(['eshop.layouts.*', 'components.*'], function ($view) {
            try {
                $languageService = app(LanguageService::class);
                $languages = $languageService->getActive();
                $selectedLanguage = $languageService->findByCode(session('language') ?? 'ar');

                $view->with([
                    'languages' => $languages,
                    'selectedLanguage' => $selectedLanguage,
                ]);
            } catch (\Exception $e) {
                // Gracefully fail and use defaults
                $view->with([
                    'languages' => [
                        (object)['code' => 'ar', 'name' => 'العربية', 'direction' => 'rtl', 'is_default' => true],
                        (object)['code' => 'en', 'name' => 'English', 'direction' => 'ltr'],
                    ],
                    'selectedLanguage' => (object)['code' => 'ar', 'name' => 'العربية', 'direction' => 'rtl'],
                ]);
            }
        });
    }
}
