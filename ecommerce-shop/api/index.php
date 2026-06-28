<?php
/**
 * Vercel Serverless Entry Point for Laravel 12 (ecommerce-shop backend)
 *
 * - Storage writes go to /tmp (only writable dir on Vercel lambda).
 * - Bootstrap caches (config/routes/views/events) are baked into the lambda
 *   via artisan cache commands at build time; we keep them under /tmp during
 *   build so they don't end up in the deployment artifact, then Laravel
 *   falls back to compiling them on-demand into /tmp on first request.
 * - If RUN_MIGRATIONS_ON_BOOT=true, runs migrate --seed on first cold start
 *   (idempotent: uses migrations table + firstOrCreate for seeders).
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

// 6. Run migrations on first cold start (idempotent via marker file)
if (getenv('RUN_MIGRATIONS_ON_BOOT') === 'true') {
    $markerFile = $storageRoot . '/migrations_complete.txt';

    if (!file_exists($markerFile)) {
        try {
            // Boot a kernel instance so we have access to Artisan
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            // Run migrations (force = no confirmation prompt)
            $exitCode = $kernel->call('migrate', ['--force' => true]);

            // Run seeders only if users table is empty (idempotent)
            try {
                $db = $app->make('db');
                $userCount = $db->table('users')->count();
                if ($userCount === 0) {
                    $kernel->call('db:seed', ['--force' => true]);
                }
            } catch (\Throwable $e) {
                // Seeding failed — not fatal, continue serving requests
                error_log('Seed failed: ' . $e->getMessage());
            }

            // Write marker file so we don't re-run on every cold start
            @file_put_contents(
                $markerFile,
                json_encode([
                    'migrated_at' => date('c'),
                    'exit_code'   => $exitCode,
                ])
            );
        } catch (\Throwable $e) {
            // Migration failed — log and continue (DB might be misconfigured)
            error_log('Migration failed: ' . $e->getMessage());
        }
    }
}

// 7. Handle request
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
