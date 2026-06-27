<?php
/**
 * Debug Logger - لقراءة آخر الأخطاء
 * استخدام: php debug_logs.php
 */

$logFile = 'storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Log file not found!\n";
    exit(1);
}

// Get last 100 lines
$lines = array_slice(file($logFile), -100);

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║         DEBUG LOGS - CHECKOUT TROUBLESHOOTING           ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

// Filter important lines
$checkoutLogs = [];
foreach ($lines as $line) {
    if (
        strpos($line, 'STOCK VALIDATION') !== false ||
        strpos($line, 'ORDER CREATION') !== false ||
        strpos($line, 'Product ID') !== false ||
        strpos($line, 'Requested Quantity') !== false ||
        strpos($line, 'Stock:') !== false ||
        strpos($line, 'Comparing:') !== false ||
        strpos($line, 'Stock validation') !== false ||
        strpos($line, 'Validation Errors') !== false ||
        strpos($line, 'Cart items') !== false ||
        strpos($line, 'API Response') !== false ||
        strpos($line, '✓') !== false ||
        strpos($line, 'insufficient') !== false ||
        strpos($line, 'ERROR') !== false ||
        strpos($line, 'WARNING') !== false
    ) {
        $checkoutLogs[] = $line;
    }
}

if (empty($checkoutLogs)) {
    echo "❌ No checkout logs found. Try checkout again!\n";
    echo "\n";
    echo "📝 Latest logs:\n";
    echo "---\n";
    foreach (array_slice($lines, -20) as $line) {
        echo $line;
    }
} else {
    echo "✓ Found " . count($checkoutLogs) . " relevant logs\n";
    echo "---\n\n";

    foreach ($checkoutLogs as $line) {
        // Parse log line
        if (preg_match('/\[(.*?)\] local\.(.*?): (.*)/', $line, $matches)) {
            $timestamp = $matches[1];
            $level = $matches[2];
            $message = $matches[3];

            $levelColor = match($level) {
                'ERROR' => '❌ ERROR',
                'WARNING' => '⚠️  WARNING',
                'INFO' => 'ℹ️  INFO',
                default => $level
            };

            echo "[$timestamp] $levelColor\n";
            echo "  $message\n\n";
        } else {
            echo $line . "\n";
        }
    }
}

echo "---\n";
echo "💡 Tips:\n";
echo "  1. Look for 'Product ID:' lines\n";
echo "  2. Check 'Requested Quantity' vs 'Stock'\n";
echo "  3. If 'Stock insufficient' appears = مخزون ناقص\n";
echo "  4. If 'API Response' is empty = مشكلة في API\n";
echo "  5. If 'Exception' appears = خطأ في النظام\n";
echo "\n";
