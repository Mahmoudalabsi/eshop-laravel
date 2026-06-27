<?php
/**
 * Test Order Creation on API
 * Simulates what the checkout does
 */

require 'bootstrap/app.php';

use App\Services\ApiService;
use Illuminate\Support\Facades\Session;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get API service
$api = app(ApiService::class);

echo "\n=======================================================\n";
echo "🧪 ORDER CREATION API TEST\n";
echo "=======================================================\n\n";

// Sample order payload
$testOrderData = [
    'user_id' => 1,
    'customer_name' => 'محمود العبسي',
    'customer_email' => 'test@example.com',
    'customer_phone' => '0501234567',
    'shipping_address' => [
        'address' => 'شارع العروبة',
        'city' => 'الرياض',
        'postal_code' => '12345'
    ],
    'billing_address' => [
        'address' => 'شارع العروبة',
        'city' => 'الرياض',
        'postal_code' => '12345'
    ],
    'shipping_cost' => 0,
    'currency_code' => 'SAR',
    'notes' => 'Test order from CLI',
    'items' => [
        [
            'product_id' => 1,
            'product_name' => 'Test Product',
            'quantity' => 1,
            'unit_price' => 100,
            'attributes' => null
        ]
    ]
];

echo "📤 Sending order payload to API...\n";
echo "Endpoint: /orders\n";
echo "Payload: " . json_encode($testOrderData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

try {
    $response = $api->post('/orders', $testOrderData);

    echo "📥 API Response:\n";
    echo "Response Data: " . json_encode($response->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    if ($response->get('error')) {
        echo "❌ ERROR: " . $response->get('message') . "\n";
    } else {
        echo "✅ SUCCESS: Order created\n";
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=======================================================\n";
echo "Check storage/logs/laravel.log for detailed API logs\n";
echo "=======================================================\n";
?>
