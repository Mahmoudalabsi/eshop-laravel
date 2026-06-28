<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $productService;

    public function __construct(CategoryService $categoryService, ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();
        return view('categories.index', compact('categories'));
    }

    public function show($id, Request $request)
    {
        $params = $request->all();
        $params['category_id'] = $id;

        $response = $this->productService->getAll($params);
        $items = $response->get('items') ?? [];
        $meta = (array) $response->get('meta');

        // تحويل المنتجات إلى كائنات لضمان عمل السهم -> في Blade
        $items = collect($items)->map(fn($item) => (object) $item)->all();

        // Set default pagination values
        $meta = array_merge([
            'total' => count($items),
            'per_page' => 12,
            'current_page' => $request->input('page', 1)
        ], $meta);

        $products = new LengthAwarePaginator(
            $items,
            $meta['total'],
            $meta['per_page'],
            $meta['current_page'],
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // جلب القسم
        $category = $this->categoryService->find($id)
                    ?? (object)['id' => $id, 'name' => __('messages.category_not_found')];

        return view('products.index', compact('category', 'products'));
    }

    public function adminIndex()
    {
        $categories = $this->categoryService->getAll();
        return view('admin.categories.index', compact('categories'));
    }

    public function adminEdit($id, ApiService $api)
    {
        $category = $this->categoryService->find($id);
        $guidesResponse = $api->get('/size-guides');
        $guides = $guidesResponse->get('data', []);

        return view('admin.categories.edit', compact('category', 'guides'));
    }

    public function adminUpdate(Request $request, $id, ApiService $api)
    {
        $response = $api->put("/categories/$id", $request->all());
        if ($response->get('error')) {
            return back()->with('error', __('messages.category_update_error'));
        }
        return redirect()->route('admin.categories.index')->with('success', __('messages.category_updated_success'));
    }
}
