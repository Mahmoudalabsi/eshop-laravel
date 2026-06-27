<?php
/**
 * View Order Creation Logs
 * Run: php view_order_logs.php
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Log file not found: $logFile\n";
    exit(1);
}

// Read the log file (last 200 lines)
$lines = array_slice(file($logFile), -200);
$content = implode('', $lines);

// Extract only ORDER-related logs
$lines = explode("\n", $content);
$orderLogs = [];

foreach ($lines as $line) {
    if (strpos($line, 'ORDER') !== false ||
        strpos($line, 'API Response') !== false ||
        strpos($line, 'API Call') !== false ||
        strpos($line, 'Stock validation') !== false ||
        strpos($line, 'Order creation') !== false ||
        strpos($line, 'Order payload') !== false) {
        $orderLogs[] = $line;
    }
}

echo "\n";
echo "=======================================================\n";
echo "📋 ORDER CREATION LOGS (Last 200 lines)\n";
echo "=======================================================\n\n";

if (empty($orderLogs)) {
    echo "❌ No order-related logs found in the last 200 lines.\n";
    echo "\n📂 Showing last 20 lines of log file:\n";
    echo "-------------------------------------------------------\n";
    $lastLines = array_slice($lines, -20);
    foreach ($lastLines as $line) {
        if (!empty(trim($line))) {
            echo $line . "\n";
        }
    }
} else {
    foreach ($orderLogs as $log) {
        if (!empty(trim($log))) {
            echo $log . "\n";
        }
    }
}

echo "\n";
echo "=======================================================\n";
echo "💡 Tips:\n";
echo "- Look for 'API Response' to see what the API returned\n";
echo "- Look for 'Order payload' to see what was sent\n";
echo "- Look for 'API Call Exception' for connection errors\n";
echo "=======================================================\n";
?>
