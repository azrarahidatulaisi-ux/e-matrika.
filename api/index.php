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

// Forward all requests to the CodeIgniter front controller in public/index.php
require __DIR__ . '/../public/index.php';

