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
// Only runs if DB_CONNECTION=sqlite — for pgsql (Supabase) this is skipped.
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
// NOTE: For pgsql (Supabase), the migrations table persists in the remote DB,
// so the marker file in /tmp is just a per-instance optimization to avoid
// querying the migrations table on every request.
if (getenv('RUN_MIGRATIONS_ON_BOOT') === 'true') {
    $markerFile = $storageRoot . '/migrations_complete.txt';

    if (!file_exists($markerFile)) {
        try {
            // Boot a kernel instance so we have access to Artisan
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            // Disable FK constraints during migration (SQLite-only)
            $dbDriver = null;
            try {
                $db = $app->make('db');
                $dbDriver = $db->connection()->getDriverName();
                if ($dbDriver === 'sqlite') {
                    $db->statement('PRAGMA foreign_keys = OFF');
                }
                // For pgsql, no special action needed.
            } catch (\Throwable $e) {}

            // Run migrations (force = no confirmation prompt)
            $exitCode = $kernel->call('migrate', ['--force' => true]);

            // Run SetupController (Vercel-safe replacement for db:seed)
            // Idempotent: only seeds when users table is empty
            try {
                $db = $app->make('db');
                $userCount = $db->table('users')->count();
                if ($userCount === 0) {
                    $setup = $app->make(\App\Http\Controllers\SetupController::class);
                    $seedLog = $setup->runSilent();
                    error_log('Setup: ' . implode(' | ', $seedLog));
                }
            } catch (\Throwable $e) {
                // Seeding failed — not fatal, continue serving requests
                error_log('Setup failed: ' . $e->getMessage());
            }

            // Re-enable FK constraints (SQLite only)
            try {
                if ($dbDriver === 'sqlite') {
                    $db = $app->make('db');
                    $db->statement('PRAGMA foreign_keys = ON');
                }
            } catch (\Throwable $e) {}

            // Write marker file so we don't re-run on every cold start
            @file_put_contents(
                $markerFile,
                json_encode([
                    'migrated_at' => date('c'),
                    'exit_code'   => $exitCode,
                    'driver'      => $dbDriver,
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

// 7d. Database connection test endpoint
if (isset($_SERVER['REQUEST_URI']) && strtok($_SERVER['REQUEST_URI'], '?') === '/_debug/db') {
    header('Content-Type: application/json');
    $out = [
        'env' => [
            'DB_CONNECTION' => getenv('DB_CONNECTION'),
            'DB_HOST' => getenv('DB_HOST'),
            'DB_PORT' => getenv('DB_PORT'),
            'DB_DATABASE' => getenv('DB_DATABASE'),
            'DB_USERNAME' => getenv('DB_USERNAME'),
            'DB_PASSWORD_set' => getenv('DB_PASSWORD') ? 'yes (' . strlen(getenv('DB_PASSWORD')) . ' chars)' : 'NO',
            'DB_SSLMODE' => getenv('DB_SSLMODE'),
            'APP_ENV' => getenv('APP_ENV'),
            'APP_DEBUG' => getenv('APP_DEBUG'),
        ],
        'pdo_pgsql_loaded' => extension_loaded('pdo_pgsql'),
        'pgsql_loaded' => extension_loaded('pgsql'),
    ];

    // Try connecting via raw pg_connect (uses pgsql extension, different network stack)
    if (extension_loaded('pgsql')) {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT') ?: '5432';
        $db   = getenv('DB_DATABASE') ?: 'postgres';
        $user = getenv('DB_USERNAME') ?: 'postgres';
        $pass = getenv('DB_PASSWORD') ?: '';
        $sslmode = getenv('DB_SSLMODE') ?: 'require';
        $connStr = "host=$host port=$port dbname=$db user=$user password=$pass sslmode=$sslmode";
        $out['pg_connect_connstr'] = $connStr;
        try {
            $pg = @pg_connect($connStr, PGSQL_CONNECT_FORCE_NEW);
            if ($pg) {
                $out['pg_connect_status'] = 'OK';
                $r = pg_query($pg, "SELECT count(*) AS c FROM users;");
                if ($r) {
                    $row = pg_fetch_assoc($r);
                    $out['pg_users_count'] = $row['c'];
                }
                pg_close($pg);
            } else {
                $out['pg_connect_status'] = 'FAILED: ' . pg_last_error();
            }
        } catch (\Throwable $e) {
            $out['pg_connect_status'] = 'EXCEPTION: ' . $e->getMessage();
        }
    }

    // Try connecting via Laravel/PDO
    try {
        $kernel->bootstrap();
        $db = $app->make('db');
        $conn = $db->connection();
        $out['connection_name'] = $conn->getName();
        $out['pdo_driver'] = $conn->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        $out['server_version'] = $conn->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
        $result = $conn->select('SELECT 1 as test');
        $out['query_result'] = $result;
        try {
            $tables = $conn->select("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename");
            $out['tables'] = array_map(fn($t) => $t->tablename, $tables);
            $out['tables_count'] = count($out['tables']);
        } catch (\Throwable $e) {
            $out['tables_error'] = $e->getMessage();
        }
        $out['status'] = 'OK';
    } catch (\Throwable $e) {
        $out['status'] = 'ERROR';
        $out['error_class'] = get_class($e);
        $out['error_message'] = $e->getMessage();
        $out['error_file'] = $e->getFile() . ':' . $e->getLine();
        $out['error_trace'] = array_slice(explode("\n", $e->getTraceAsString()), 0, 15);
    }
    echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
