<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
   protected $fillable = [
        'name', 'discount_value', 'type', 'scope', 'target_id', 'starts_at', 'ends_at', 'status'
    ];

    public function getTargetName()
    {
        if ($this->scope == 'product') {
            return Product::find($this->target_id)?->name;
        } elseif ($this->scope == 'category') {
            return Category::find($this->target_id)?->name;
        }
        return 'كل المتجر';
    }
}
