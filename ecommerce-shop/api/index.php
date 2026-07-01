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

// 5b. WORKAROUND: Manually register + boot all configured service providers.
//
// On Vercel PHP 8.3 runtime, the RegisterProviders bootstrapper is not
// running reliably (likely because an earlier bootstrapper throws and is
// caught silently, leaving $app->hasBeenBootstrapped=true but providers
// never registered). This causes $app->make('db') to fail with
// "Target class [db] does not exist".
//
// Fix: explicitly register all providers from config('app.providers')
// and boot them. This is idempotent — if providers are already registered,
// the register() call is a no-op.
$providerFixLog = "/tmp/storage/provider_fix.log";
@file_put_contents($providerFixLog, "=== Fix run " . date('c') . " ===\n");
try {
    $dbBound = $app->bound('db');
    $configBound = $app->bound('config');
    @file_put_contents($providerFixLog, "db_bound=" . ($dbBound ? 'true' : 'false') . " config_bound=" . ($configBound ? 'true' : 'false') . "\n", FILE_APPEND);
    
    if (! $dbBound) {
        // Need to load config first if not already loaded
        if (! $configBound) {
            // Create empty Repository and bind it FIRST so that config files
            // can use the config() helper during loading (e.g. sanctum.php
            // calls config('app.url') via Sanctum::currentApplicationUrlWithPort).
            $config = new \Illuminate\Config\Repository();
            $app->instance('config', $config);
            @file_put_contents($providerFixLog, "Bound empty config instance\n", FILE_APPEND);
            
            // Now load config files
            $configPath = $basePath . '/config';
            if (is_dir($configPath)) {
                $loadedKeys = [];
                foreach (glob($configPath . '/*.php') as $configFile) {
                    $key = basename($configFile, '.php');
                    try {
                        $config->set($key, require $configFile);
                        $loadedKeys[] = $key;
                    } catch (\Throwable $ce) {
                        @file_put_contents($providerFixLog, "Failed to load config/$key.php: " . $ce->getMessage() . "\n", FILE_APPEND);
                    }
                }
                @file_put_contents($providerFixLog, "Loaded config keys: " . implode(',', $loadedKeys) . "\n", FILE_APPEND);
            }
        }
        $providers = $app->make('config')->get('app.providers', []);
        @file_put_contents($providerFixLog, "Providers count: " . count($providers) . "\n", FILE_APPEND);
        
        $registered = [];
        $skipped = [];
        foreach ($providers as $providerClass) {
            if (! class_exists($providerClass)) {
                $skipped[] = $providerClass . ' (class not found)';
                continue;
            }
            $reflection = new \ReflectionProperty($app, 'loadedProviders');
            $reflection->setAccessible(true);
            $loaded = $reflection->getValue($app);
            if (isset($loaded[$providerClass])) {
                $skipped[] = $providerClass . ' (already loaded)';
                continue;
            }
            try {
                $app->register($providerClass);
                $registered[] = $providerClass;
            } catch (\Throwable $re) {
                $skipped[] = $providerClass . ' (register failed: ' . $re->getMessage() . ')';
            }
        }
        @file_put_contents($providerFixLog, "Registered: " . count($registered) . " Skipped: " . count($skipped) . "\n", FILE_APPEND);
        if (! empty($skipped)) {
            @file_put_contents($providerFixLog, "Skipped details: " . implode(' | ', $skipped) . "\n", FILE_APPEND);
        }
        
        if (! $app->isBooted()) {
            $app->boot();
            @file_put_contents($providerFixLog, "Booted: true\n", FILE_APPEND);
        } else {
            @file_put_contents($providerFixLog, "Already booted\n", FILE_APPEND);
        }
        
        @file_put_contents($providerFixLog, "db_bound_after=" . ($app->bound('db') ? 'true' : 'false') . "\n", FILE_APPEND);
    } else {
        @file_put_contents($providerFixLog, "db already bound, skipping\n", FILE_APPEND);
    }
} catch (\Throwable $e) {
    @file_put_contents($providerFixLog, "EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
}

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

// 7e. Bootstrap + log dump endpoint
if (isset($_SERVER['REQUEST_URI']) && strtok($_SERVER['REQUEST_URI'], '?') === '/_debug/bootstrap') {
    header('Content-Type: application/json');
    $out = [
        'php_version' => PHP_VERSION,
        'has_been_bootstrapped_before' => $app->hasBeenBootstrapped(),
    ];
    // Try bootstrap
    try {
        $kernel->bootstrap();
        $out['bootstrap_status'] = 'OK';
        $out['has_been_bootstrapped_after'] = $app->hasBeenBootstrapped();
        // Check config
        try {
            $config = $app->make('config');
            $out['config_app_providers'] = $config->get('app.providers', '(not set)');
            $out['config_database_default'] = $config->get('database.default', '(not set)');
            $out['config_database_connections'] = array_keys($config->get('database.connections', []));
            $out['config_db_connection'] = $config->get('database.connections.' . $config->get('database.default', 'pgsql') . '.driver', '(missing)');
        } catch (\Throwable $e) {
            $out['config_error'] = $e->getMessage();
        }
        // Get bound services
        try {
            $bindings = $app->getBindings();
            $out['bindings_count'] = count($bindings);
            $out['has_db_binding'] = isset($bindings['db']);
            $out['has_db_connection'] = $app->bound('db');
            $out['bindings_keys'] = array_keys($bindings);
            // Try to get the registered service provider classes
            try {
                $loadedProviders = $app->getLoadedProviders();
                $out['loaded_providers_count'] = count($loadedProviders);
                $out['loaded_providers'] = array_keys($loadedProviders);
            } catch (\Throwable $e) {
                $out['loaded_providers_error'] = $e->getMessage();
            }
        } catch (\Throwable $e) {
            $out['bindings_error'] = $e->getMessage();
        }
        // Check if bootstrap/cache files exist
        $out['bootstrap_cache_files'] = [];
        $cacheDir = $basePath . '/bootstrap/cache';
        if (is_dir($cacheDir)) {
            foreach (scandir($cacheDir) as $f) {
                if ($f === '.' || $f === '..') continue;
                $path = $cacheDir . '/' . $f;
                $out['bootstrap_cache_files'][$f] = is_file($path) ? filesize($path) : 'dir';
            }
        } else {
            $out['bootstrap_cache_files'] = 'no cache dir at ' . $cacheDir;
        }
        // Now try make('db')
        try {
            $db = $app->make('db');
            $out['db_make_status'] = 'OK: ' . get_class($db);
        } catch (\Throwable $e) {
            $out['db_make_status'] = 'FAILED: ' . $e->getMessage();
            // Try manually registering the DatabaseServiceProvider
            try {
                $dbProvider = new \Illuminate\Database\DatabaseServiceProvider($app);
                $dbProvider->register();
                $dbProvider->boot();
                $out['manual_register_status'] = 'OK';
                $db = $app->make('db');
                $out['db_make_after_manual_register'] = 'OK: ' . get_class($db);
                // Try a query
                try {
                    $conn = $db->connection();
                    $result = $conn->select('SELECT 1 AS test');
                    $out['db_query_test'] = 'OK';
                    $out['db_driver'] = $conn->getDriverName();
                } catch (\Throwable $qe) {
                    $out['db_query_test'] = 'FAILED: ' . $qe->getMessage();
                }
            } catch (\Throwable $re) {
                $out['manual_register_status'] = 'FAILED: ' . $re->getMessage();
            }
        }
    } catch (\Throwable $e) {
        $out['bootstrap_status'] = 'FAILED: ' . get_class($e) . ': ' . $e->getMessage();
        $out['bootstrap_file'] = $e->getFile() . ':' . $e->getLine();
        $out['bootstrap_trace'] = array_slice(explode("\n", $e->getTraceAsString()), 0, 10);
    }
    // Read Laravel log file
    $logPath = '/tmp/storage/logs/laravel.log';
    if (file_exists($logPath)) {
        $content = @file_get_contents($logPath);
        if ($content === false) {
            $out['laravel_log'] = 'file exists but unreadable';
        } else {
            // Take last 8000 chars
            $out['laravel_log_tail'] = substr($content, max(0, strlen($content) - 8000));
        }
    } else {
        $out['laravel_log'] = 'no log file at ' . $logPath;
    }
    // Read provider fix log
    $fixLogPath = '/tmp/storage/provider_fix.log';
    if (file_exists($fixLogPath)) {
        $content = @file_get_contents($fixLogPath);
        if ($content === false) {
            $out['provider_fix_log'] = 'file exists but unreadable';
        } else {
            $out['provider_fix_log'] = substr($content, max(0, strlen($content) - 8000));
        }
    } else {
        $out['provider_fix_log'] = 'no fix log at ' . $fixLogPath;
    }
    echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
