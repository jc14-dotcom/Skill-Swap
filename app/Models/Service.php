<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Service extends Model
{
	protected string $table = 'services';

	public function listPublic(?string $searchQuery = null): array
	{
		$sql = 'SELECT s.*, u.name AS provider_name, u.profile_picture AS provider_profile_picture
				FROM services s
				INNER JOIN users u ON u.id = s.user_id
				WHERE s.status = :status';

		$params = ['status' => 'active'];
		$keyword = trim((string) $searchQuery);

		if ($keyword !== '') {
			$sql .= ' AND (
				s.title LIKE :keyword_title
				OR s.description LIKE :keyword_description
				OR u.name LIKE :keyword_provider
			)';
			$params['keyword_title'] = '%' . $keyword . '%';
			$params['keyword_description'] = '%' . $keyword . '%';
			$params['keyword_provider'] = '%' . $keyword . '%';
		}

		$sql .= ' ORDER BY s.id DESC';

		return $this->query($sql, $params)->fetchAll();
	}

	public function findWithProvider(int $id): ?array
	{
		$sql = 'SELECT s.*, u.name AS provider_name, u.email AS provider_email, u.profile_picture AS provider_profile_picture
				FROM services s
				INNER JOIN users u ON u.id = s.user_id
				WHERE s.id = :id
				LIMIT 1';

		$row = $this->query($sql, ['id' => $id])->fetch();
		return is_array($row) ? $row : null;
	}

	public function listByProvider(int $providerId): array
	{
		return $this->where('user_id', $providerId, 'id DESC');
	}

	public function createService(array $payload): int
	{
		return $this->insert([
			'user_id' => $payload['user_id'],
			'title' => $payload['title'],
			'description' => $payload['description'],
			'price' => $payload['price'],
			'status' => $payload['status'] ?? 'active',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function updateService(int $serviceId, array $payload): bool
	{
		return $this->updateById($serviceId, [
			'title' => $payload['title'],
			'description' => $payload['description'],
			'price' => $payload['price'],
			'status' => $payload['status'],
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function deleteService(int $serviceId): bool
	{
		$statement = $this->query('DELETE FROM services WHERE id = :id LIMIT 1', ['id' => $serviceId]);
		return $statement->rowCount() > 0;
	}

	public function hasBookings(int $serviceId): bool
	{
		$row = $this->query('SELECT id FROM bookings WHERE service_id = :service_id LIMIT 1', [
			'service_id' => $serviceId,
		])->fetch();

		return is_array($row);
	}
}
