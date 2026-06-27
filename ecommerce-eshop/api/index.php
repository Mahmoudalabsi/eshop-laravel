<?php
/**
 * Vercel Serverless Entry Point for Laravel 12
 * Uses /tmp as the only writable directory for Laravel storage.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);
$tmpStorage = '/tmp/laravel-storage';

// ----- 1. Ensure /tmp/storage exists -----
@mkdir($tmpStorage . '/framework/cache/data', 0777, true);
@mkdir($tmpStorage . '/framework/sessions', 0777, true);
@mkdir($tmpStorage . '/framework/views', 0777, true);
@mkdir($tmpStorage . '/logs', 0777, true);

// ----- 2. Redirect Laravel storage subdirs to /tmp via custom constants -----
// We can't replace the storage/ directory (it's bundled), so we override
// the path Laravel uses at runtime by setting environment-friendly defaults.
$appStorage = $basePath . '/storage';
foreach (['framework', 'logs'] as $sub) {
    $link = $appStorage . '/' . $sub;
    $target = $tmpStorage . '/' . $sub;

    // Skip if already a symlink pointing to /tmp
    if (is_link($link) && readlink($link) === $target) {
        continue;
    }

    // Try to remove existing directory/symlink
    if (is_dir($link) && !is_link($link)) {
        // Recursively delete (Laravel's storage/framework subdirs are typically empty in fresh installs)
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($link, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $fileinfo->isDir() ? @rmdir($fileinfo->getRealPath()) : @unlink($fileinfo->getRealPath());
        }
        @rmdir($link);
    } elseif (is_link($link)) {
        @unlink($link);
    }

    // Create symlink
    if (!file_exists($link)) {
        @symlink($target, $link);
    }
}

// ----- 3. Maintenance mode (skip on Vercel — managed via DB instead) -----
// We don't read storage/framework/maintenance.php here because storage is symlinked to /tmp
// which is empty across cold starts. Use APP_MAINTENANCE_DRIVER=database instead.

// ----- 4. Boot Laravel -----
require $basePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
