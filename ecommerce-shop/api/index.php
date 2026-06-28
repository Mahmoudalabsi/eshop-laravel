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

// 0. Fix Vercel's SCRIPT_NAME override so Laravel resolves routes correctly.
// Vercel's PHP runtime sets SCRIPT_NAME to the request URI (e.g. "/api/v1/products")
// which causes Symfony to compute a wrong baseUrl. We need SCRIPT_NAME to be empty
// so that baseUrl = "" and pathInfo = requestUri (the full path).
// Reference: Symfony Request::prepareBaseUrl() uses SCRIPT_NAME as baseUrl.
$_SERVER['SCRIPT_NAME'] = '';
$_SERVER['SCRIPT_FILENAME'] = '';
if (isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = '';
}
if (isset($_SERVER['ORIG_SCRIPT_NAME'])) {
    $_SERVER['ORIG_SCRIPT_NAME'] = '';
}

// 1. Ensure /tmp storage subdirs exist (writable on Vercel)
$storageRoot = '/tmp/storage';
@mkdir($storageRoot . '/framework/cache/data', 0777, true);
@mkdir($storageRoot . '/framework/sessions', 0777, true);
@mkdir($storageRoot . '/framework/views', 0777, true);
@mkdir($storageRoot . '/logs', 0777, true);
@mkdir($storageRoot . '/app/public', 0777, true);

// 1b. Ensure SQLite database file exists (touch creates empty file if missing)
$sqlitePath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
if (getenv('DB_CONNECTION') === 'sqlite' && !file_exists($sqlitePath)) {
    @touch($sqlitePath);
}

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

// 6b. Diagnostic: log registered routes (helps debug 404 issues on Vercel)
if (getenv('APP_DEBUG') === 'true') {
    try {
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $routes = $app->make('router')->getRoutes();
        $routeList = [];
        foreach ($routes as $r) {
            $routeList[] = $r->methods()[0] . ' ' . $r->uri();
        }
        error_log('Registered routes: ' . implode(', ', array_slice($routeList, 0, 30)));
    } catch (\Throwable $e) {
        error_log('Route diagnostic failed: ' . $e->getMessage());
    }
}

// 7. Handle request
$kernel = $app->make(Kernel::class);

// 7b. Debug endpoint: list all registered routes
if (isset($_SERVER['REQUEST_URI']) && strtok($_SERVER['REQUEST_URI'], '?') === '/_debug/routes') {
    try {
        $kernel->bootstrap();
        $routes = $app->make('router')->getRoutes();
        $routeList = [];
        foreach ($routes as $r) {
            $methods = implode(',', $r->methods());
            $routeList[] = $methods . ' ' . $r->uri() . ' → ' . ($r->getActionName() ?? '?');
        }

        // Capture what Laravel's Request sees
        $req = Request::capture();
        header('Content-Type: application/json');
        echo json_encode([
            'count' => count($routeList),
            'routes' => $routeList,
            'server_SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? '?',
            'server_SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? '?',
            'server_REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '?',
            'server_PATH_INFO' => $_SERVER['PATH_INFO'] ?? '?',
            'laravel_path_info' => $req->getPathInfo(),
            'laravel_base_url' => $req->getBaseUrl(),
            'laravel_request_uri' => $req->getRequestUri(),
        ], JSON_PRETTY_PRINT);
        exit;
    } catch (\Throwable $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        exit;
    }
}

// 7c. Debug endpoint that captures the real path being requested
// This must match BEFORE the kernel handles the request, so we can see what Laravel WOULD see
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/_debug/path') === 0) {
    // For this debug endpoint, manually capture what would happen for a different path
    $testPath = '/api/v1/products';
    $origRequestUri = $_SERVER['REQUEST_URI'];
    $_SERVER['REQUEST_URI'] = $testPath;

    $req = Request::capture();

    // Restore
    $_SERVER['REQUEST_URI'] = $origRequestUri;

    try {
        $kernel->bootstrap();
        $router = $app->make('router');
        $routes = $router->getRoutes();
        $matchedRoutes = [];
        foreach ($routes as $r) {
            if (in_array('GET', $r->methods())) {
                $matchedRoutes[] = $r->uri();
                if ($r->uri() === 'api/v1/products') {
                    $matchedRoutes[] = '  ← MATCH FOUND';
                }
            }
        }
    } catch (\Throwable $e) {
        $matchedRoutes = ['error: ' . $e->getMessage()];
    }

    header('Content-Type: application/json');
    echo json_encode([
        'test_path' => $testPath,
        'laravel_path_info' => $req->getPathInfo(),
        'laravel_path' => $req->path(),
        'laravel_base_url' => $req->getBaseUrl(),
        'laravel_request_uri' => $req->getRequestUri(),
        'laravel_method' => $req->getMethod(),
        'api_routes_count' => count(array_filter($matchedRoutes, fn($r) => strpos($r, 'api/') === 0)),
        'sample_api_routes' => array_slice(array_filter($matchedRoutes, fn($r) => strpos($r, 'api/') === 0), 0, 5),
        'found_match' => in_array('  ← MATCH FOUND', $matchedRoutes),
    ], JSON_PRETTY_PRINT);
    exit;
}

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
