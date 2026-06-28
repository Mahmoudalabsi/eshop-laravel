<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get all active categories with subcategories.
     */
    public function getAll()
    {
        return Category::with(['subcategories' => function ($q) {
            $q->where('status', 1);
        }])
        ->where('status', 1)
        ->orderBy('name')
        ->get()
        ->map(fn($c) => (object) $c->toArray());
    }

    /**
     * Find a category by ID with subcategories.
     */
    public function find($id)
    {
        $cat = Category::with(['subcategories' => function ($q) {
            $q->where('status', 1);
        }])->find($id);

        if (!$cat) {
            return null;
        }

        return (object) $cat->toArray();
    }
}
