<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    protected $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function index()
    {
        $languages = $this->languageService->getAll();
        return view('admin.languages.index', compact('languages'));
    }

    public function getLanguagesJson()
    {
        return response()->json($this->languageService->getAll());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:languages,code',
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'boolean',
            'status' => 'boolean',
        ]);

        $lang = Language::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'flag' => $validated['flag'] ?? null,
            'direction' => $validated['direction'],
            'is_default' => $validated['is_default'] ?? false,
            'status' => $validated['status'] ?? true,
        ]);

        return response()->json(['message' => 'تمت إضافة اللغة بنجاح', 'data' => $lang]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2',
            'flag' => 'nullable|string',
            'direction' => 'required|in:ltr,rtl',
            'is_default' => 'boolean',
            'status' => 'boolean',
        ]);

        $lang = Language::findOrFail($id);
        $lang->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'flag' => $validated['flag'] ?? null,
            'direction' => $validated['direction'],
            'is_default' => $validated['is_default'] ?? false,
            'status' => $validated['status'] ?? true,
        ]);

        return response()->json(['message' => 'تم تحديث اللغة بنجاح', 'data' => $lang]);
    }

    public function updateStatus(Request $request, $id)
    {
        $lang = Language::findOrFail($id);
        $lang->update(['status' => $request->boolean('status')]);
        return response()->json(['message' => 'تم تحديث الحالة بنجاح']);
    }

    public function destroy($id)
    {
        $lang = Language::findOrFail($id);
        if ($lang->is_default) {
            return response()->json(['message' => 'لا يمكن حذف اللغة الافتراضية'], 422);
        }
        $lang->delete();
        return response()->json(['message' => 'تم حذف اللغة بنجاح']);
    }

    public function setLanguage($code)
    {
        $language = $this->languageService->findByCode($code);

        if (!$language && in_array($code, ['ar', 'en'])) {
            $language = (object) ['code' => $code, 'direction' => $code === 'ar' ? 'rtl' : 'ltr'];
        }

        if ($language) {
            session()->put('locale', $code);
            session()->put('language', $code);
            session()->put('language_direction', $language->direction ?? 'ltr');
            app()->setLocale($code);
        }

        return redirect()->back();
    }
}
