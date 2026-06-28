<?php

namespace App\Http\Controllers;

use App\Models\SizeGuide;
use Illuminate\Http\Request;

class SizeGuideController extends Controller
{
    public function adminIndex()
    {
        $guides = SizeGuide::with('category')->get();
        return view('admin.size-guides.index', compact('guides'));
    }

    public function adminCreate()
    {
        return view('admin.size-guides.create');
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sizes' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        SizeGuide::create($validated);

        return redirect()->route('admin.size-guides.index')
            ->with('success', __('messages.size_guide_saved_success'));
    }

    public function adminEdit($id)
    {
        $guide = SizeGuide::findOrFail($id);
        return view('admin.size-guides.edit', compact('guide'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sizes' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $guide = SizeGuide::findOrFail($id);
        $guide->update($validated);

        return redirect()->route('admin.size-guides.index')
            ->with('success', __('messages.size_guide_updated_success'));
    }

    public function adminDestroy($id)
    {
        SizeGuide::findOrFail($id)->delete();
        return redirect()->route('admin.size-guides.index')
            ->with('success', __('messages.size_guide_deleted_success'));
    }
}
