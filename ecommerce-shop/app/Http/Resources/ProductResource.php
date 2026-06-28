<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $price = (float) $this->price;
        $oldPrice = (float) $this->old_price;
        $discountLabel = '0%';
        
        if ($oldPrice > 0 && $oldPrice > $price) {
            $discountLabel = round((($oldPrice - $price) / $oldPrice) * 100) . '%';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description ? strip_tags($this->description) : '',
            
            // Simple fields for Blade
            'price' => $price,
            'old_price' => $oldPrice,
            'image' => str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image),
            
            'stock_status' => [
                'total_qty' => (int) $this->attributes->sum('qty'),
                'available' => $this->status == 1 && $this->attributes->sum('qty') > 0,
            ],
            'pricing' => [
                'price' => $price,
                'old_price' => $oldPrice,
                'discount' => $discountLabel,
            ],
            'media' => [
                'thumbnail' => str_starts_with($this->image, 'http') ? $this->image : asset('storage/' . $this->image),
                'gallery' => $this->images->map(fn($img) => str_starts_with($img->image_path, 'http') ? $img->image_path : asset('storage/' . $img->image_path)),
            ],
            'details' => [
                'category' => $this->subcategory?->category?->name ?? 'عام',
                'subcategory' => $this->subcategory?->name ?? null,
                'rating' => (float) ($this->reviews_avg_rating ?? $this->average_rating ?? 0),
                'reviews' => (int) ($this->reviews_count ?? 0),
                'size_guide' => $this->subcategory?->category?->sizeGuide ? [
                    'name' => $this->subcategory->category->sizeGuide->name,
                    'content' => $this->subcategory->category->sizeGuide->content,
                ] : null,
            ],
            'options' => $this->attributes->map(fn($attr) => [
                'id' => $attr->id,
                'size' => $attr->size,
                'color' => $attr->color,
                'qty' => $attr->qty,
            ]),
            'reviews_list' => $this->relationLoaded('reviews') ? $this->reviews->map(fn($rev) => [
                'id' => $rev->id,
                'user_name' => $rev->user->name ?? 'عميل مجهول',
                'rating' => (int) $rev->rating,
                'comment' => $rev->comment,
                'date' => $rev->created_at->diffForHumans(),
            ]) : [],
        ];
    }
}
