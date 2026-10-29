<?php

declare(strict_types=1);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Register fatal shutdown handler to catch errors before exit
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo "<h1>Fatal Startup Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($error['message']) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($error['file']) . " on line " . $error['line'] . "</p>";
    }
});

// Map underscore env vars from Vercel to CodeIgniter's expected dot notation
$envMappings = [
    'app_baseURL' => 'app.baseURL',
    'encryption_key' => 'encryption.key',
    'database_default_hostname' => 'database.default.hostname',
    'database_default_database' => 'database.default.database',
    'database_default_username' => 'database.default.username',
    'database_default_password' => 'database.default.password',
    'database_default_port' => 'database.default.port',
    'database_default_DBDriver' => 'database.default.DBDriver',
];

foreach ($envMappings as $vercelKey => $ciKey) {
    $val = getenv($vercelKey) ?: ($_ENV[$vercelKey] ?? null);
    if ($val !== null && $val !== false) {
        putenv("{$ciKey}={$val}");
        $_ENV[$ciKey] = $val;
        $_SERVER[$ciKey] = $val;
    }
}

// Adjust REQUEST_URI if needed
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

require __DIR__ . '/../public/index.php';




