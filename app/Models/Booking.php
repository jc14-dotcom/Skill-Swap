<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Booking extends Model
{
	protected string $table = 'bookings';

	public function createBooking(array $payload): int
	{
		return $this->insert([
			'service_id' => $payload['service_id'],
			'seeker_id' => $payload['seeker_id'],
			'provider_id' => $payload['provider_id'],
			'status' => $payload['status'] ?? 'pending',
			'message' => $payload['message'] ?? null,
			'scheduled_at' => $payload['scheduled_at'] ?? null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function findDetailed(int $bookingId): ?array
	{
		$sql = 'SELECT b.*, s.title AS service_title,
					   seeker.name AS seeker_name,
					   provider.name AS provider_name
				FROM bookings b
				INNER JOIN services s ON s.id = b.service_id
				INNER JOIN users seeker ON seeker.id = b.seeker_id
				INNER JOIN users provider ON provider.id = b.provider_id
				WHERE b.id = :id
				LIMIT 1';

		$row = $this->query($sql, ['id' => $bookingId])->fetch();
		return is_array($row) ? $row : null;
	}

	public function listForSeeker(int $seekerId): array
	{
		$sql = 'SELECT b.*, s.title AS service_title, u.name AS provider_name
				FROM bookings b
				INNER JOIN services s ON s.id = b.service_id
				INNER JOIN users u ON u.id = b.provider_id
				WHERE b.seeker_id = :seeker_id
				ORDER BY b.id DESC';

		return $this->query($sql, ['seeker_id' => $seekerId])->fetchAll();
	}

	public function listForProvider(int $providerId): array
	{
		$sql = 'SELECT b.*, s.title AS service_title, u.name AS seeker_name
				FROM bookings b
				INNER JOIN services s ON s.id = b.service_id
				INNER JOIN users u ON u.id = b.seeker_id
				WHERE b.provider_id = :provider_id
				ORDER BY b.id DESC';

		return $this->query($sql, ['provider_id' => $providerId])->fetchAll();
	}

	public function listForProviderService(int $providerId, int $serviceId): array
	{
		$sql = 'SELECT b.*, s.title AS service_title, u.name AS seeker_name, u.profile_picture AS seeker_profile_picture
				FROM bookings b
				INNER JOIN services s ON s.id = b.service_id
				INNER JOIN users u ON u.id = b.seeker_id
				WHERE b.provider_id = :provider_id
				  AND b.service_id = :service_id
				ORDER BY b.id DESC';

		return $this->query($sql, [
			'provider_id' => $providerId,
			'service_id' => $serviceId,
		])->fetchAll();
	}

	public function updateStatus(int $bookingId, string $status): bool
	{
		return $this->updateById($bookingId, [
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}
}
