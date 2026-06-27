<?php
require 'bootstrap/app.php';
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$products = DB::table('products')
    ->select('id', 'name', 'stock', 'price')
    ->where('stock', '>', 0)
    ->limit(10)
    ->get();

echo "=== Available Products ===\n\n";
foreach($products as $p) {
    echo "ID: {$p->id}\n";
    echo "Name: {$p->name}\n";
    echo "Stock: {$p->stock}\n";
    echo "Price: {$p->price} SAR\n";
    echo "---\n";
}

if(count($products) == 0) {
    echo "❌ No products found in stock!\n";
} else {
    echo "\n✓ Found " . count($products) . " products in stock\n";
}
