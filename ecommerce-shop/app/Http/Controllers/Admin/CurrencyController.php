<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        return view('eshop.dashboard.currencies');
    }

    public function getCurrenciesJson()
    {
        $currencies = Currency::orderBy('is_default', 'desc')->get();
        return response()->json($currencies);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|size:3',
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric',
        ]);

        if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
            $request->merge(['exchange_rate' => 1.0]);
        }

        Currency::create($request->all());

        return response()->json(['message' => 'Currency added successfully']);
    }

    public function update(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'code' => 'required|size:3',
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric',
        ]);

        if ($request->is_default) {
            Currency::where('is_default', true)->update(['is_default' => false]);
            $request->merge(['exchange_rate' => 1.0]);
        }

        $currency->update($request->all());

        return response()->json(['message' => 'Currency updated successfully']);
    }

    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        if ($currency->is_default) {
            return response()->json(['message' => 'Cannot delete default currency'], 422);
        }
        $currency->delete();
        return response()->json(['message' => 'Currency deleted successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);
        $currency->update(['status' => !$currency->status]);
        return response()->json(['message' => 'Status updated']);
    }
}
