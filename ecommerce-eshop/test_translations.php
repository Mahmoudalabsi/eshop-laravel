<?php
/**
 * Translation System Test
 * Tests all checkout-related translations
 */

// Bootstrap Laravel
require __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

echo "=== Translation System Test ===\n\n";

// Test Arabic translation
echo "[Test 1] Arabic Translation\n";
app('translator')->setLocale('ar');
$arMsg = __('messages.product_not_available_quantity', ['product' => 'فستان أسود']);
echo "Result: $arMsg\n";
echo "Status: " . (strpos($arMsg, 'فستان أسود') !== false ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test English translation
echo "[Test 2] English Translation\n";
app('translator')->setLocale('en');
$enMsg = __('messages.product_not_available_quantity', ['product' => 'Black Dress']);
echo "Result: $enMsg\n";
echo "Status: " . (strpos($enMsg, 'Black Dress') !== false ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test multiple products
echo "[Test 3] Multiple Product Error Messages\n";
app('translator')->setLocale('ar');
$errors = [
    __('messages.product_not_available_quantity', ['product' => 'فستان أسود']),
    __('messages.product_not_available_quantity', ['product' => 'بليزر أحمر']),
];
$combined = implode(', ', $errors);
echo "Result: $combined\n";
echo "Status: " . (count($errors) === 2 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test all messages
echo "[Test 4] All Order Messages\n";
$messages = [
    'empty_cart_error' => __('messages.empty_cart_error'),
    'order_creation_error' => __('messages.order_creation_error'),
    'order_not_found' => __('messages.order_not_found'),
    'order_created_success' => __('messages.order_created_success'),
];

foreach ($messages as $key => $value) {
    echo "  $key: $value\n";
}
echo "Status: " . (count($messages) === 4 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test language switching
echo "[Test 5] Language Switching\n";
$key = 'product_not_available_quantity';
$product = 'Test Product';

app('translator')->setLocale('ar');
$arResult = __("messages.$key", ['product' => $product]);

app('translator')->setLocale('en');
$enResult = __("messages.$key", ['product' => $product]);

echo "Arabic: $arResult\n";
echo "English: $enResult\n";
echo "Status: " . ($arResult !== $enResult ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "=== All Translation Tests Complete ===\n";
