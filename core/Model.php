<?php

declare(strict_types=1);

namespace Core;

abstract class Model
{
	protected string $table = '';
	protected string $primaryKey = 'id';

	protected function query(string $sql, array $params = []): \PDOStatement
	{
		return Database::query($sql, $params);
	}

	public function all(string $orderBy = 'id DESC'): array
	{
		$this->assertIdentifier($orderBy, true);

		$sql = sprintf('SELECT * FROM %s ORDER BY %s', $this->table, $orderBy);
		return $this->query($sql)->fetchAll();
	}

	public function find(int|string $id): ?array
	{
		$sql = sprintf('SELECT * FROM %s WHERE %s = :id LIMIT 1', $this->table, $this->primaryKey);
		$row = $this->query($sql, ['id' => $id])->fetch();
		return is_array($row) ? $row : null;
	}

	public function firstWhere(string $column, mixed $value): ?array
	{
		$this->assertIdentifier($column);

		$sql = sprintf('SELECT * FROM %s WHERE %s = :value LIMIT 1', $this->table, $column);
		$row = $this->query($sql, ['value' => $value])->fetch();
		return is_array($row) ? $row : null;
	}

	public function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
	{
		$this->assertIdentifier($column);
		$this->assertIdentifier($orderBy, true);

		$sql = sprintf('SELECT * FROM %s WHERE %s = :value ORDER BY %s', $this->table, $column, $orderBy);
		return $this->query($sql, ['value' => $value])->fetchAll();
	}

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

	private function assertIdentifier(string $identifier, bool $allowClause = false): void
	{
		$pattern = $allowClause
			? '/^[a-zA-Z0-9_\s,\.]+$/'
			: '/^[a-zA-Z0-9_\.]+$/';

		if (!preg_match($pattern, $identifier)) {
			throw new \InvalidArgumentException('Unsafe SQL identifier: ' . $identifier);
		}
	}
}
