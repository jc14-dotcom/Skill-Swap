<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Notification extends Model
{
	protected string $table = 'notifications';

	public function createNotification(array $payload): int
	{
		return $this->insert([
			'user_id' => $payload['user_id'],
			'actor_id' => $payload['actor_id'] ?? null,
			'type' => $payload['type'],
			'title' => $payload['title'],
			'body' => $payload['body'] ?? null,
			'link' => $payload['link'] ?? null,
			'meta_json' => $payload['meta_json'] ?? null,
			'read_at' => null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function listForUser(int $userId, int $limit = 10): array
	{
		$limit = max(1, min($limit, 100));

		$sql = 'SELECT n.*, actor.name AS actor_name
				FROM notifications n
				LEFT JOIN users actor ON actor.id = n.actor_id
				WHERE n.user_id = :user_id
				ORDER BY n.id DESC
				LIMIT ' . $limit;

		return $this->query($sql, ['user_id' => $userId])->fetchAll();
	}

	public function unreadCount(int $userId): int
	{
		$row = $this->query(
			'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND read_at IS NULL',
			['user_id' => $userId]
		)->fetch();

		return (int) ($row['total'] ?? 0);
	}

	public function findForUser(int $notificationId, int $userId): ?array
	{
		$row = $this->query(
			'SELECT * FROM notifications WHERE id = :id AND user_id = :user_id LIMIT 1',
			[
				'id' => $notificationId,
				'user_id' => $userId,
			]
		)->fetch();

		return is_array($row) ? $row : null;
	}

	public function markAsRead(int $notificationId, int $userId): bool
	{
		$statement = $this->query(
			'UPDATE notifications
			 SET read_at = :read_at,
				 updated_at = :updated_at
			 WHERE id = :id
			   AND user_id = :user_id
			   AND read_at IS NULL',
			[
				'read_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'id' => $notificationId,
				'user_id' => $userId,
			]
		);

		return $statement->rowCount() > 0;
	}

	public function markAllAsRead(int $userId): int
	{
		$statement = $this->query(
			'UPDATE notifications
			 SET read_at = :read_at,
				 updated_at = :updated_at
			 WHERE user_id = :user_id
			   AND read_at IS NULL',
			[
				'read_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'user_id' => $userId,
			]
		);

		return $statement->rowCount();
	}
}
