<?php
/**
 * Simple test endpoint - bypass Laravel to verify PHP runtime works.
 * Visit /api/test.php to see PHP environment details.
 */
header('Content-Type: application/json');

echo json_encode([
    'status' => 'PHP is working on Vercel!',
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
    'vercel' => getenv('VERCEL') ? 'yes' : 'no',
    'region' => getenv('VERCEL_REGION') ?? 'unknown',
    'app_debug' => getenv('APP_DEBUG'),
    'app_env' => getenv('APP_ENV'),
    'db_connection' => getenv('DB_CONNECTION'),
    'db_host' => getenv('DB_HOST') ? 'set' : 'NOT SET',
    'tmp_writable' => is_writable('/tmp') ? 'yes' : 'no',
    'storage_exists' => is_dir(dirname(__DIR__) . '/storage') ? 'yes' : 'no',
    'vendor_exists' => is_dir(dirname(__DIR__) . '/vendor') ? 'yes' : 'no',
    'autoload_exists' => file_exists(dirname(__DIR__) . '/vendor/autoload.php') ? 'yes' : 'no',
    'time' => date('Y-m-d H:i:s'),
], JSON_PRETTY_PRINT);
