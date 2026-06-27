<?php
/**
 * End-to-End Test: Checkout Flow
 * Tests: Login → Get Products → Validate Stock → Create Order
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiBase = 'http://127.0.0.1:8000/api';

function makeRequest($method, $endpoint, $data = null, $token = null) {
    global $apiBase;

    $url = $apiBase . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $headers = ['Content-Type: application/json'];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

echo "=== E2E Test: Order Creation ===\n\n";

// Step 1: Login
echo "Step 1: Authenticate\n";
echo "Email: admin@elegance.com\n";
echo "Password: admin123\n";

$loginRes = makeRequest('POST', '/v1/login', [
    'email' => 'admin@elegance.com',
    'password' => 'admin123',
    'device_name' => 'test_device'
]);

if ($loginRes['code'] != 200) {
    echo "❌ Login failed (HTTP {$loginRes['code']})\n";
    echo json_encode($loginRes['data'], JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

$token = $loginRes['data']['data']['access_token'] ?? null;
if (!$token) {
    echo "❌ No API token received\n";
    echo json_encode($loginRes['data'], JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

echo "✅ Login successful\n";
echo "Token: " . substr($token, 0, 20) . "...\n\n";

// Step 2: Get Products (to verify stock)
echo "Step 2: Fetch Products\n";
$productsRes = makeRequest('GET', '/v1/products');
if ($productsRes['code'] != 200) {
    echo "❌ Failed to fetch products (HTTP {$productsRes['code']})\n";
    print_r($productsRes);
    exit(1);
}

$products = $productsRes['data']['data'] ?? [];
echo "✅ Retrieved " . count($products) . " products\n";

if (count($products) < 1) {
    echo "❌ No products available for order\n";
    exit(1);
}

// Get first product with available stock
$selectedProduct = null;
foreach ($products as $product) {
    $qty = $product['stock_status']['total_qty'] ?? 0;
    if ($qty > 0) {
        $selectedProduct = $product;
        echo "Selected: {$product['name']} (Stock: {$qty})\n";
        break;
    }
}

if (!$selectedProduct) {
    echo "❌ No products with available stock\n";
    exit(1);
}

echo "\n";

// Step 3: Create Order
echo "Step 3: Create Order\n";

$orderPayload = [
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '0500000000',
    'shipping_address' => [
        'street' => 'Test Shipping Address',
        'city' => 'City',
        'country' => 'Country',
        'zip' => '00000'
    ],
    'billing_address' => [
        'street' => 'Test Billing Address',
        'city' => 'City',
        'country' => 'Country',
        'zip' => '00000'
    ],
    'shipping_cost' => 50,
    'currency_code' => 'SAR',
    'items' => [
        [
            'product_id' => $selectedProduct['id'],
            'product_name' => $selectedProduct['name'],
            'quantity' => 1,
            'unit_price' => $selectedProduct['price'] ?? 0
        ]
    ]
];

echo "Payload:\n";
echo json_encode($orderPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

$orderRes = makeRequest('POST', '/v1/orders', $orderPayload, $token);

if ($orderRes['code'] >= 400) {
    echo "❌ Order creation failed (HTTP {$orderRes['code']})\n";
    echo json_encode($orderRes['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(1);
}

echo "✅ Order created successfully (HTTP {$orderRes['code']})\n";
echo json_encode($orderRes['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

$orderId = $orderRes['data']['data']['id'] ?? null;
if ($orderId) {
    echo "\n✅ Order ID: {$orderId}\n";
    echo "✅ Full flow completed successfully!\n";
} else {
    echo "\n⚠️ No order ID returned\n";
}
