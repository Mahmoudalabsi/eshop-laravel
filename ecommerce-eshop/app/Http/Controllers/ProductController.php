<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $params = array_merge($request->all(), [
            'per_page' => $request->input('per_page', 12),
            'sort' => $request->input('sort'),
            'offers' => $request->input('offers'),
            'max_price' => $request->input('max_price'),
            'color' => $request->input('color'),
            'size' => $request->input('size'),
            'category_id' => $request->input('category_id'),
            'search' => $request->input('q')
        ]);

        $response = $this->productService->getAll($params);
        $items = $response->get('items');
        $meta = (array) $response->get('meta');

        $filters = $this->productService->getFilters();

        // Set default pagination values if meta is empty
        $meta = array_merge([
            'total' => 0,
            'per_page' => 12,
            'current_page' => $request->input('page', 1)
        ], $meta);

        $products = new LengthAwarePaginator(
            $items,
            $meta['total'] ?? 0,
            $meta['per_page'] ?? 12,
            $meta['current_page'] ?? 1,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $html .= '<div class="col-6 col-md-4">' . view('components.product-card', compact('product'))->render() . '</div>';
            }
            return response()->json([
                'html' => $html,
                'total' => $meta['total'],
                'pagination' => (string) $products->links()
            ]);
        }

        return view('products.index', compact('products', 'filters'));
    }

    public function show($id)
    {
        try {
            $product = $this->productService->find($id);
            $relatedProducts = $this->productService->getRelated($id, 5);

            return view('products.show', compact('product', 'relatedProducts'));
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                          ->with('error', __('messages.product_not_found'));
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return $request->ajax() ? response()->json([]) : view('products.search', ['products' => [], 'query' => $query]);
        }

        $products = $this->productService->search($query, 10);

        if ($request->ajax()) {
            return response()->json($products);
        }

        return view('products.search', compact('products', 'query'));
    }

    public function featured()
    {
        $products = $this->productService->getFeatured(12);

        return view('products.featured', compact('products'));
    }

    public function onOffer()
    {
        $products = $this->productService->getOnOffer(20);

        return view('products.on-offer', compact('products'));
    }

    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $response = $this->productService->submitReview([
                'product_id' => $id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            if ($response->get('status') === 'error') {
                return redirect()->back()->with('error', $response->get('message', __('messages.review_error')));
            }

            return redirect()->back()->with('success', __('messages.review_submitted_success'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.review_submission_error') . $e->getMessage());
        }
    }
}
