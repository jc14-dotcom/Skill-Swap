<?php

declare(strict_types=1);

namespace Core;

/**
 * Base Active Record model class.
 *
 * Provides database CRUD operations with SQL injection protection.
 * Extend this class and set $table and $primaryKey for your model.
 *
 * @template T of array<string, mixed>
 */
abstract class Model
{
	/** @var string Database table name */
	protected string $table = '';

	/** @var string Primary key column name */
	protected string $primaryKey = 'id';

	/**
	 * Execute a raw SQL query with parameters.
	 *
	 * @param string $sql SQL query with named placeholders
	 * @param array<string, mixed> $params Bound parameters
	 * @return \PDOStatement
	 */
	protected function query(string $sql, array $params = []): \PDOStatement
	{
		return Database::query($sql, $params);
	}

	/**
	 * Get all records from the table.
	 *
	 * @param string $orderBy ORDER BY clause (e.g., 'id DESC', 'name ASC')
	 * @return array<int, T>
	 */
	public function all(string $orderBy = 'id DESC'): array
	{
		$this->assertOrderBy($orderBy);

		$sql = sprintf('SELECT * FROM %s ORDER BY %s', $this->table, $orderBy);
		return $this->query($sql)->fetchAll();
	}

	/**
	 * Find a record by its primary key.
	 *
	 * @param int|string $id Primary key value
	 * @return T|null The record array or null if not found
	 */
	public function find(int|string $id): ?array
	{
		$sql = sprintf('SELECT * FROM %s WHERE %s = :id LIMIT 1', $this->table, $this->primaryKey);
		$row = $this->query($sql, ['id' => $id])->fetch();
		return is_array($row) ? $row : null;
	}

	/**
	 * Find the first record matching a column value.
	 *
	 * @param string $column Column name to match
	 * @param mixed $value Value to match
	 * @return T|null The record array or null if not found
	 */
	public function firstWhere(string $column, mixed $value): ?array
	{
		$this->assertIdentifier($column);

		$sql = sprintf('SELECT * FROM %s WHERE %s = :value LIMIT 1', $this->table, $column);
		$row = $this->query($sql, ['value' => $value])->fetch();
		return is_array($row) ? $row : null;
	}

	/**
	 * Get all records matching a column value.
	 *
	 * @param string $column Column name to match
	 * @param mixed $value Value to match
	 * @param string $orderBy ORDER BY clause
	 * @return array<int, T>
	 */
	public function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
	{
		$this->assertIdentifier($column);
		$this->assertOrderBy($orderBy);

		$sql = sprintf('SELECT * FROM %s WHERE %s = :value ORDER BY %s', $this->table, $column, $orderBy);
		return $this->query($sql, ['value' => $value])->fetchAll();
	}

	/**
	 * Insert a new record.
	 *
	 * @param array<string, mixed> $data Column => value pairs
	 * @return int The inserted record's primary key
	 * @throws \InvalidArgumentException If data is empty
	 */
	public function insert(array $data): int
	{
		if ($data === []) {
			throw new \InvalidArgumentException('Insert data cannot be empty.');
		}

		$columns = array_keys($data);
		foreach ($columns as $column) {
			$this->assertIdentifier((string) $column);
		}

		$placeholders = array_map(static fn(string $column): string => ':' . $column, $columns);

		$sql = sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$this->table,
			implode(', ', $columns),
			implode(', ', $placeholders)
		);

		$this->query($sql, $data);
		return Database::lastInsertId();
	}

	/**
	 * Update a record by its primary key.
	 *
	 * @param int|string $id Primary key value
	 * @param array<string, mixed> $data Column => value pairs to update
	 * @return bool True if a row was updated
	 */
	public function updateById(int|string $id, array $data): bool
	{
		if ($data === []) {
			return false;
		}

		$assignments = [];
		foreach ($data as $column => $value) {
			$this->assertIdentifier((string) $column);
			$assignments[] = $column . ' = :' . $column;
		}

		$sql = sprintf(
			'UPDATE %s SET %s WHERE %s = :__id',
			$this->table,
			implode(', ', $assignments),
			$this->primaryKey
		);

		$params = $data;
		$params['__id'] = $id;
		$statement = $this->query($sql, $params);

		return $statement->rowCount() > 0;
	}

	/**
	 * Validate that a string is a safe SQL identifier (column/table name).
	 *
	 * @throws \InvalidArgumentException If identifier contains unsafe characters
	 */
	private function assertIdentifier(string $identifier): void
	{
		if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
			throw new \InvalidArgumentException('Unsafe SQL identifier: ' . $identifier);
		}
	}

	/**
	 * Validate that an ORDER BY clause contains only safe column names and directions.
	 *
	 * @throws \InvalidArgumentException If clause contains unsafe content
	 */
	private function assertOrderBy(string $clause): void
	{
		$segments = array_map('trim', explode(',', $clause));
		$columnPattern = '/^[a-zA-Z_][a-zA-Z0-9_]*$/';
		$directionPattern = '/^(ASC|DESC)$/i';

		foreach ($segments as $segment) {
			$parts = preg_split('/\s+/', $segment);
			$column = $parts[0] ?? '';

			if (!preg_match($columnPattern, $column)) {
				throw new \InvalidArgumentException('Unsafe column in ORDER BY: ' . $column);
			}

			$direction = $parts[1] ?? null;
			if ($direction !== null && !preg_match($directionPattern, $direction)) {
				throw new \InvalidArgumentException('Invalid sort direction: ' . $direction);
			}
		}
	}
}
