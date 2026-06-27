<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeGuide;
use Illuminate\Http\Request;

class SizeGuideController extends Controller
{
    public function index()
    {
        $guides = SizeGuide::all();
        return view('eshop.dashboard.size-guides.index', compact('guides'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        SizeGuide::create($request->all());

        return response()->json(['message' => 'تمت إضافة دليل المقاسات بنجاح']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string'
        ]);

        $guide = SizeGuide::findOrFail($id);
        $guide->update($request->all());

        return response()->json(['message' => 'تم تحديث دليل المقاسات بنجاح']);
    }

    public function destroy($id)
    {
        SizeGuide::findOrFail($id)->delete();
        return response()->json(['message' => 'تم حذف دليل المقاسات بنجاح']);
    }

    public function getGuidesJson()
    {
        return response()->json(SizeGuide::all());
    }
}
