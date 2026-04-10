<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$autoload = static function (string $class): void {
    $prefixes = [
        'Core\\' => BASE_PATH . '/core/',
        'App\\' => BASE_PATH . '/app/',
        'Database\\Seeders\\' => BASE_PATH . '/database/seeders/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
};

spl_autoload_register($autoload);

$envFile = BASE_PATH . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($value !== '' && (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        )) {
            $value = substr($value, 1, -1);
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

$config = require BASE_PATH . '/config/database.php';

if (strtolower((string) ($config['driver'] ?? 'mysql')) === 'mysql') {
    $dbName = (string) ($config['database'] ?? 'skillswap');
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
        throw new RuntimeException('Unsafe database name in configuration.');
    }

    $host = (string) ($config['host'] ?? '127.0.0.1');
    $port = (string) ($config['port'] ?? '3306');
    $username = (string) ($config['username'] ?? 'root');
    $password = (string) ($config['password'] ?? '');
    $charset = (string) ($config['charset'] ?? 'utf8mb4');

    $bootstrapPdo = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=%s', $host, $port, $charset),
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $bootstrapPdo->exec(
        sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s_unicode_ci',
            $dbName,
            $charset,
            $charset
        )
    );
}

Core\Database::connect($config);

$migrationFiles = glob(BASE_PATH . '/database/migrations/*.sql') ?: [];
sort($migrationFiles);

foreach ($migrationFiles as $file) {
    $sql = trim((string) file_get_contents($file));
    if ($sql === '') {
        continue;
    }

    Core\Database::pdo()->exec($sql);
    echo 'Migrated: ' . basename($file) . PHP_EOL;
}

echo 'All migrations complete.' . PHP_EOL;
