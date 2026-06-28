<?php
/**
 * Vercel Serverless Entry Point for Laravel 12 (ecommerce-eshop frontend)
 *
 * - Storage writes go to /tmp (only writable dir on Vercel lambda).
 * - This frontend app talks to the ecommerce-shop backend via API_BASE_URL.
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);

// 1. Ensure /tmp storage subdirs exist (writable on Vercel)
$storageRoot = '/tmp/storage';
@mkdir($storageRoot . '/framework/cache/data', 0777, true);
@mkdir($storageRoot . '/framework/sessions', 0777, true);
@mkdir($storageRoot . '/framework/views', 0777, true);
@mkdir($storageRoot . '/logs', 0777, true);
@mkdir($storageRoot . '/app/public', 0777, true);

// 2. Ensure /tmp bootstrap cache exists
@mkdir('/tmp/bootstrap/cache', 0777, true);

// 3. Load Composer autoloader
require $basePath . '/vendor/autoload.php';

// 4. Boot Laravel application
/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

// 5. Override storage path to /tmp (writable on serverless)
$app->useStoragePath($storageRoot);

// 6. Handle request
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
