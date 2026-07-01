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

// 0. Fix Vercel's SCRIPT_NAME override so Laravel resolves routes correctly.
// Vercel's PHP runtime sets SCRIPT_NAME to the request URI (e.g. "/api/v1/products")
// which causes Symfony to compute a wrong baseUrl. We need SCRIPT_NAME to be empty
// so that baseUrl = "" and pathInfo = requestUri (the full path).
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
try {
    if (! $app->bound('db')) {
        // Need to load config first if not already loaded
        if (! $app->bound('config')) {
            // Create empty Repository and bind it FIRST so that config files
            // can use the config() helper during loading (e.g. sanctum.php
            // calls config('app.url') via Sanctum::currentApplicationUrlWithPort).
            $config = new \Illuminate\Config\Repository();
            $app->instance('config', $config);
            
            // Now load config files
            $configPath = $basePath . '/config';
            if (is_dir($configPath)) {
                foreach (glob($configPath . '/*.php') as $configFile) {
                    $key = basename($configFile, '.php');
                    try {
                        $config->set($key, require $configFile);
                    } catch (\Throwable $ce) {
                        // Silently skip config files that fail to load
                    }
                }
            }
        }
        $providers = $app->make('config')->get('app.providers', []);
        // Merge Laravel's default providers (normally done by LoadConfiguration bootstrapper)
        try {
            $defaultProviders = new \Illuminate\Support\DefaultProviders();
            $providers = array_unique(array_merge($providers, $defaultProviders->toArray()));
        } catch (\Throwable $de) {
            // Silently continue
        }
        foreach ($providers as $providerClass) {
            if (! class_exists($providerClass)) {
                continue;
            }
            $reflection = new \ReflectionProperty($app, 'loadedProviders');
            $reflection->setAccessible(true);
            $loaded = $reflection->getValue($app);
            if (isset($loaded[$providerClass])) {
                continue;
            }
            $app->register($providerClass);
        }
        if (! $app->isBooted()) {
            $app->boot();
        }
    }
} catch (\Throwable $e) {
    error_log('Manual provider registration failed: ' . $e->getMessage());
}

// 5c. Run migrations on EVERY request (Vercel lambda /tmp is ephemeral)
// Always try migrations because /tmp doesn't persist between cold starts
// NOTE: For pgsql (Supabase), the migrations table persists in the remote DB,
// so this is a no-op after the first successful run.
if (getenv('RUN_MIGRATIONS_ON_BOOT') === 'true') {
    $migrationLog = "/tmp/storage/migration.log";
    @file_put_contents($migrationLog, "=== Migration run " . date('c') . " ===\n");

    try {
        $migKernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $migKernel->bootstrap();

        // Disable FK constraints during migration (SQLite-only; PostgreSQL uses
        // session_replication_role but we don't need it because migrations are
        // ordered correctly and use Schema builder which is FK-safe).
        $dbDriver = null;
        try {
            $db = $app->make('db');
            $dbDriver = $db->connection()->getDriverName();
            if ($dbDriver === 'sqlite') {
                $db->statement('PRAGMA foreign_keys = OFF');
            }
            // For pgsql, no special action needed — Laravel Schema handles FKs.
        } catch (\Throwable $e) {}

        // Run standard migrate (grammar is patched in vercel-build.sh to support old SQLite)
        try {
            $exitCode = $migKernel->call('migrate', ['--force' => true]);
            $migOutput = $migKernel->output();
            @file_put_contents($migrationLog, "MIGRATE EXIT: $exitCode\nDRIVER: $dbDriver\nOUTPUT:\n$migOutput\n", FILE_APPEND);
        } catch (\Throwable $e) {
            @file_put_contents($migrationLog, "MIGRATE FAILED: " . $e->getMessage() . "\n", FILE_APPEND);
        }

        // Seed if no users - use SetupController directly (no artisan db:seed,
        // no faker dependency — fully Vercel-safe)
        try {
            $db = $app->make('db');
            $userCount = $db->table('users')->count();
            if ($userCount === 0) {
                $setup = $app->make(\App\Http\Controllers\SetupController::class);
                $seedLog = $setup->runSilent();
                @file_put_contents($migrationLog, "SEED: completed\n" . implode("\n", $seedLog) . "\n", FILE_APPEND);
            } else {
                @file_put_contents($migrationLog, "SEED: skipped (users exist: $userCount)\n", FILE_APPEND);
            }
        } catch (\Throwable $e) {
            @file_put_contents($migrationLog, "SEED ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
        }

        // Re-enable FK constraints (SQLite only)
        try {
            if ($dbDriver === 'sqlite') {
                $db = $app->make('db');
                $db->statement('PRAGMA foreign_keys = ON');
            }
        } catch (\Throwable $e) {}
    } catch (\Throwable $e) {
        @file_put_contents($migrationLog, "BOOTSTRAP FAILED: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

// 6. Handle request
$kernel = $app->make(Kernel::class);

// 6a. Simple debug endpoints (check before Laravel router)
$reqUri = $_SERVER['REQUEST_URI'] ?? '/';
$reqPath = parse_url($reqUri, PHP_URL_PATH) ?: '/';

if ($reqPath === '/_debug/migrate' || $reqPath === '/_debug/migration-log') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "=== Migration Log ===\n";
    echo @file_get_contents('/tmp/storage/migration.log') ?: "(no log file)\n";
    echo "\n=== DB File ===\n";
    $dbPath = getenv('DB_DATABASE') ?: '/tmp/database.sqlite';
    echo "Path: $dbPath\n";
    echo "Exists: " . (file_exists($dbPath) ? 'YES (' . filesize($dbPath) . ' bytes)' : 'NO') . "\n";
    echo "\n=== SQLite Version ===\n";
    try {
        $db = $app->make('db');
        $pdo = $db->connection()->getPdo();
        echo $pdo->query('SELECT sqlite_version()')->fetchColumn() . "\n";
        echo "PHP PDO SQLite version: " . $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION) . "\n";
        echo "\n=== Test pragma_table_info ===\n";
        try {
            $r = $pdo->query("SELECT name FROM pragma_table_info('users') LIMIT 3");
            echo "pragma_table_info('users'): " . json_encode($r->fetchAll(\PDO::FETCH_COLUMN)) . "\n";
        } catch (\Throwable $e) {
            echo "pragma_table_info('users') ERROR: " . $e->getMessage() . "\n";
        }
        echo "\n=== Test PRAGMA table_info(users) (old syntax) ===\n";
        try {
            $r = $pdo->query("PRAGMA table_info(users)");
            echo "PRAGMA table_info(users): " . json_encode($r->fetchAll(\PDO::FETCH_ASSOC)) . "\n";
        } catch (\Throwable $e) {
            echo "PRAGMA table_info(users) ERROR: " . $e->getMessage() . "\n";
        }
    } catch (\Throwable $e) {
        echo "Version check failed: " . $e->getMessage() . "\n";
    }
    echo "\n=== Tables ===\n";
    try {
        $db = $app->make('db');
        $pdo = $db->connection()->getPdo();
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(\PDO::FETCH_COLUMN);
        echo implode("\n", $tables) ?: "(no tables)";
    } catch (\Throwable $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
    exit;
}

// 6b. Old debug endpoint removed (replaced by 6a above)

$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();

$kernel->terminate($request, $response);
