<?php
/**
 * Vercel Serverless Entry Point for Laravel 12
 * Minimal version — no symlinks (Vercel lambda fs is mostly read-only).
 * Storage writes go to /tmp directly via Laravel config.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);

// Ensure /tmp storage subdirs exist (writable on Vercel)
@mkdir('/tmp/storage/framework/cache/data', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir('/tmp/storage/framework/views', 0777, true);
@mkdir('/tmp/storage/logs', 0777, true);

// Boot Laravel
require $basePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

// Override storage paths at runtime to use /tmp (writable on serverless)
$app->useStoragePath('/tmp/storage');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
