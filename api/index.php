<?php

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

// Set error reporting to catch issues
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    define('FCPATH', realpath(__DIR__ . '/../public') . DIRECTORY_SEPARATOR);
    chdir(FCPATH);

    // Forward all requests to the CodeIgniter front controller in public/index.php
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Application Error (500)</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}



