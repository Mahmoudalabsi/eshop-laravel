<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class SizeGuideController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function adminIndex()
    {
        $response = $this->api->get('/size-guides');
        $guides = $response->get('data', []);
        return view('admin.size-guides.index', compact('guides'));
    }

    public function adminCreate()
    {
        return view('admin.size-guides.create');
    }

    public function adminStore(Request $request)
    {
        $response = $this->api->post('/size-guides', $request->all());
        if ($response->get('error')) {
            return back()->with('error', __('messages.size_guide_save_error'));
        }
        return redirect()->route('admin.size-guides.index')->with('success', __('messages.size_guide_saved_success'));
    }

    public function adminEdit($id)
    {
        $response = $this->api->get("/size-guides/$id");
        $guide = $response->get('data');
        return view('admin.size-guides.edit', compact('guide'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $response = $this->api->put("/size-guides/$id", $request->all());
        if ($response->get('error')) {
            return back()->with('error', __('messages.size_guide_update_error'));
        }
        return redirect()->route('admin.size-guides.index')->with('success', __('messages.size_guide_updated_success'));
    }

    public function adminDestroy($id)
    {
        $this->api->delete("/size-guides/$id");
        return redirect()->route('admin.size-guides.index')->with('success', __('messages.size_guide_deleted_success'));
    }
}
