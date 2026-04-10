<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
	private static ?PDO $pdo = null;

	public static function connect(array $config): void
	{
		if (self::$pdo instanceof PDO) {
			return;
		}

		$driver = strtolower((string) ($config['driver'] ?? 'mysql'));

		try {
			if ($driver === 'sqlite') {
				$path = (string) ($config['sqlite_path'] ?? BASE_PATH . '/storage/database.sqlite');
				$dir = dirname($path);
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
				}
				if (!is_file($path)) {
					touch($path);
				}

				$dsn = 'sqlite:' . $path;
				self::$pdo = new PDO($dsn);
			} else {
				$host = (string) ($config['host'] ?? '127.0.0.1');
				$port = (string) ($config['port'] ?? '3306');
				$database = (string) ($config['database'] ?? 'skillswap');
				$charset = (string) ($config['charset'] ?? 'utf8mb4');
				$username = (string) ($config['username'] ?? 'root');
				$password = (string) ($config['password'] ?? '');

				$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $database, $charset);
				self::$pdo = new PDO($dsn, $username, $password);
			}

			self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch (PDOException $exception) {
			throw new \RuntimeException('Database connection failed: ' . $exception->getMessage(), 0, $exception);
		}
	}

	public static function pdo(): PDO
	{
		if (!self::$pdo instanceof PDO) {
			throw new \RuntimeException('Database has not been connected.');
		}

		return self::$pdo;
	}

	public static function query(string $sql, array $params = []): PDOStatement
	{
		$statement = self::pdo()->prepare($sql);
		$statement->execute($params);
		return $statement;
	}

	public static function lastInsertId(): int
	{
		return (int) self::pdo()->lastInsertId();
	}

	public static function transaction(callable $callback): mixed
	{
		$pdo = self::pdo();
		$pdo->beginTransaction();

		try {
			$result = $callback($pdo);
			$pdo->commit();
			return $result;
		} catch (\Throwable $exception) {
			if ($pdo->inTransaction()) {
				$pdo->rollBack();
			}

			throw $exception;
		}
	}
}
