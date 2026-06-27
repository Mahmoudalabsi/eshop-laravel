<?php
/**
 * Vercel Serverless Entry Point for Laravel 12
 *
 * This file is loaded by the vercel-php runtime. It:
 *  1) Sets up the writable /tmp directory for Laravel storage on serverless.
 *  2) Symlinks/copies the framework storage directories to /tmp.
 *  3) Boots Laravel's kernel and serves the request.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ----- 1. Make storage writable on serverless -----
$tmpStorage = '/tmp/storage';
$basePath = dirname(__DIR__);

if (!is_dir($tmpStorage)) {
    @mkdir($tmpStorage . '/framework/cache/data', 0777, true);
    @mkdir($tmpStorage . '/framework/sessions', 0777, true);
    @mkdir($tmpStorage . '/framework/views', 0777, true);
    @mkdir($tmpStorage . '/logs', 0777, true);
}

// Symlink the writable storage into place (only if not already linked)
$storageLink = $basePath . '/storage';
if (!is_link($storageLink)) {
    // On Vercel, /tmp is the only writable directory. We can't replace `storage/`
    // (it's part of the deployed bundle and contains some files), so we override
    // the framework subdirectories only.
    foreach (['framework', 'logs'] as $dir) {
        $target = $storageLink . '/' . $dir;
        if (is_dir($target)) {
            // Move existing content to /tmp if any
            $tmpDir = $tmpStorage . '/' . $dir;
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0777, true);
            }
            // Replace directory with symlink to /tmp
            @rmdir($target);
            @symlink($tmpDir, $target);
        }
    }
}

// ----- 2. Maintenance mode (optional) -----
$maintenance = $basePath . '/storage/framework/maintenance.php';
if (file_exists($maintenance)) {
    require $maintenance;
}

// ----- 3. Boot Laravel -----
require $basePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

// Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
