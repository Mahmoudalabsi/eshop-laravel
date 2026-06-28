<?php

namespace App\Services;

class ProductService
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function getAll($params = [])
    {
        $response = $this->api->get('/products', $params);
        // Handle both wrapped 'data' and direct list responses
        $data = $response->get('data') ?? $response->all();
        // If it's a direct list, meta might be missing or in headers (not handled here), or absent.
        // Assuming if 'data' key exists, 'meta' might usually exist alongside it.
        $meta = $response->get('meta', []);

        // Ensure data is iterable (list of items)
        if (!is_array($data) || (function_exists('array_is_list') && !array_is_list($data) && !empty($data))) {
             $data = [];
        }

        $items = collect($data)->map(function ($item) {
            return (object) $item;
        });

        return collect([
            'items' => $items,
            'meta' => (object) $meta,
        ]);
    }

    public function find($id)
    {
        $response = $this->api->get("/products/$id");
        $data = $response->get('data') ?? $response->all();
        return $data ? (object) $data : null;
    }

    public function getFeatured($limit = 8)
    {
        return $this->getAll(['limit' => $limit, 'featured' => 1])->get('items');
    }

    public function getOffers($limit = 12)
    {
        return $this->getAll(['per_page' => $limit, 'offers' => 1])->get('items');
    }

    public function getOnOffer($limit = 12)
    {
        return $this->getOffers($limit);
    }

    public function getTopRated($limit = 4)
    {
        return $this->getAll(['per_page' => $limit, 'sort' => 'top_rated'])->get('items');
    }

    public function getRelated($productId, $limit = 5)
    {
        return $this->getAll(['per_page' => $limit, 'related_to' => $productId])->get('items');
    }

    public function search($query, $limit = 20)
    {
        return $this->getAll(['search' => $query, 'per_page' => $limit])->get('items');
    }

    public function submitReview($data)
    {
        return $this->api->post('/products/review', $data);
    }

    public function getFilters()
    {
        return $this->api->get('/filters')->all();
    }
}
