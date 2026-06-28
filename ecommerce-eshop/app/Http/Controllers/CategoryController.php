<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Models\Category;
use App\Models\SizeGuide;
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

        $category = $this->categoryService->find($id)
                    ?? (object)['id' => $id, 'name' => __('messages.category_not_found')];

        return view('products.index', compact('category', 'products'));
    }

    public function adminIndex()
    {
        $categories = $this->categoryService->getAll();
        return view('admin.categories.index', compact('categories'));
    }

    public function adminEdit($id)
    {
        $category = $this->categoryService->find($id);
        $guides = SizeGuide::all();
        return view('admin.categories.edit', compact('category', 'guides'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'size_guide_id' => 'nullable|integer|exists:size_guides,id',
        ]);

        $cat = Category::findOrFail($id);
        $cat->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 1,
            'size_guide_id' => $validated['size_guide_id'] ?? null,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_updated_success'));
    }
}
