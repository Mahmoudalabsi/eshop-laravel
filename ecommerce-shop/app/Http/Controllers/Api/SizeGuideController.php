<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SizeGuide;
use Illuminate\Http\Request;

class SizeGuideController extends Controller
{
    public function index()
    {
        return response()->json(['data' => SizeGuide::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'content' => 'required|string'
        ]);

        $guide = SizeGuide::create($request->all());
        return response()->json(['message' => 'Size guide created', 'data' => $guide]);
    }

    public function show($id)
    {
        return response()->json(['data' => SizeGuide::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $guide = SizeGuide::findOrFail($id);
        $guide->update($request->all());
        return response()->json(['message' => 'Size guide updated', 'data' => $guide]);
    }

    public function destroy($id)
    {
        SizeGuide::findOrFail($id)->delete();
        return response()->json(['message' => 'Size guide deleted']);
    }
}
