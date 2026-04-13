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
			(str_starts_with($value, '"') && str_ends_with($value, '"'))
			|| (str_starts_with($value, "'") && str_ends_with($value, "'"))
		)) {
			$value = substr($value, 1, -1);
		}

		putenv($key . '=' . $value);
		$_ENV[$key] = $value;
		$_SERVER[$key] = $value;
	}
}

$config = require BASE_PATH . '/config/database.php';
Core\Database::connect($config);
$pdo = Core\Database::pdo();

$driver = strtolower((string) ($config['driver'] ?? 'mysql'));

if ($driver !== 'mysql') {
	echo 'Rollback currently targets MySQL status-constraint flow; skipped for driver: ' . $driver . PHP_EOL;
	exit(0);
}

dropConstraintIfExists($pdo, 'bookings', 'chk_bookings_status_allowed');
dropConstraintIfExists($pdo, 'services', 'chk_services_status_allowed');
dropIndexIfExists($pdo, 'messages', 'idx_messages_booking_receiver_read');

$pdo->exec('DROP TABLE IF EXISTS booking_status_history');

echo 'Rollback complete: removed T6 hardening artifacts.' . PHP_EOL;

function dropConstraintIfExists(PDO $pdo, string $table, string $constraintName): void
{
	$statement = $pdo->prepare(
		'SELECT COUNT(*) AS total
		 FROM information_schema.TABLE_CONSTRAINTS
		 WHERE CONSTRAINT_SCHEMA = DATABASE()
		   AND TABLE_NAME = :table_name
		   AND CONSTRAINT_NAME = :constraint_name'
	);
	$statement->execute([
		'table_name' => $table,
		'constraint_name' => $constraintName,
	]);

	$total = (int) (($statement->fetch()['total'] ?? 0));
	if ($total === 0) {
		return;
	}

	$tableName = '`' . str_replace('`', '``', $table) . '`';
	$escapedConstraint = '`' . str_replace('`', '``', $constraintName) . '`';

	try {
		$pdo->exec('ALTER TABLE ' . $tableName . ' DROP CHECK ' . $escapedConstraint);
		return;
	} catch (Throwable $exception) {
		// Fallback for engines that use DROP CONSTRAINT for CHECK constraints.
	}

	$pdo->exec('ALTER TABLE ' . $tableName . ' DROP CONSTRAINT ' . $escapedConstraint);
}

function dropIndexIfExists(PDO $pdo, string $table, string $indexName): void
{
	$statement = $pdo->prepare(
		'SELECT COUNT(*) AS total
		 FROM information_schema.STATISTICS
		 WHERE TABLE_SCHEMA = DATABASE()
		   AND TABLE_NAME = :table_name
		   AND INDEX_NAME = :index_name'
	);
	$statement->execute([
		'table_name' => $table,
		'index_name' => $indexName,
	]);

	$total = (int) (($statement->fetch()['total'] ?? 0));
	if ($total === 0) {
		return;
	}

	$tableName = '`' . str_replace('`', '``', $table) . '`';
	$escapedIndex = '`' . str_replace('`', '``', $indexName) . '`';
	$pdo->exec('ALTER TABLE ' . $tableName . ' DROP INDEX ' . $escapedIndex);
}