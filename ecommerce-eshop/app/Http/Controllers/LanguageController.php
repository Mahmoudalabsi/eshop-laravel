<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use App\Services\ApiService;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    protected $languageService;
    protected $api;

    public function __construct(LanguageService $languageService, ApiService $api)
    {
        $this->languageService = $languageService;
        $this->api = $api;
    }

    /**
     * عرض صفحة إدارة اللغات
     */
    public function index()
    {
        $languages = $this->languageService->getAll();
        return view('admin.languages.index', compact('languages'));
    }

    /**
     * الحصول على جميع اللغات بصيغة JSON
     */
    public function getLanguagesJson()
    {
        $languages = $this->languageService->getAll();
        return response()->json($languages);
    }

    /**
     * إضافة لغة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2',
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'boolean',
        ]);

        try {
            $response = $this->api->post('/languages', $validated);

            if ($response->get('error')) {
                return response()->json(['message' => __('messages.language_add_failed')], 422);
            }

            return response()->json(['message' => __('messages.language_added_success'), 'data' => $response->get('data')]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.error_occurred') . $e->getMessage()], 500);
        }
    }

    /**
     * تحديث لغة
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2',
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'boolean',
        ]);

        try {
            $response = $this->api->post("/languages/$id", $validated);

            if ($response->get('error')) {
                return response()->json(['message' => __('messages.language_update_failed')], 422);
            }

            return response()->json(['message' => __('messages.language_updated_success'), 'data' => $response->get('data')]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.error_occurred') . $e->getMessage()], 500);
        }
    }

    /**
     * تحديث حالة اللغة (تفعيل/تعطيل)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $response = $this->api->post("/languages/$id/status",
                ['status' => $request->get('status', false)]
            );

            if ($response->get('error')) {
                return response()->json(['message' => __('messages.status_update_failed')], 422);
            }

            return response()->json(['message' => __('messages.status_updated_success')]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.error_occurred') . $e->getMessage()], 500);
        }
    }

    /**
     * حذف لغة
     */
    public function destroy($id)
    {
        try {
            $language = $this->languageService->find($id);

            if ($language && $language->is_default) {
                return response()->json(['message' => __('messages.cannot_delete_default_language')], 422);
            }

            $response = $this->api->post("/languages/$id/delete");
            return response()->json(['message' => __('messages.language_deleted_success')]);
        } catch (\Exception $e) {
            return response()->json(['message' => __('messages.error_occurred') . $e->getMessage()], 500);
        }
    }

    /**
     * تعيين اللغة الحالية
     */
    public function setLanguage($code)
    {
        // Try to find language via Service (API) first
        $language = $this->languageService->findByCode($code);

        // Fallback for default languages if API fails or returns nothing
        if (!$language && in_array($code, ['ar', 'en'])) {
            $language = (object) ['code' => $code, 'direction' => $code === 'ar' ? 'rtl' : 'ltr'];
        }

        if ($language) {
            session()->put('locale', $code);
            session()->put('language', $code); // Backward compatibility
            session()->put('language_direction', $language->direction ?? 'ltr');

            app()->setLocale($code);
        }

        return redirect()->back();
    }
}
