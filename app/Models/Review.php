<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Review extends Model
{
	protected string $table = 'reviews';

	public function createReview(array $payload): int
	{
		return $this->insert([
			'booking_id' => $payload['booking_id'],
			'service_id' => $payload['service_id'],
			'reviewer_id' => $payload['reviewer_id'],
			'reviewee_id' => $payload['reviewee_id'],
			'rating' => $payload['rating'],
			'comment' => $payload['comment'] ?? null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function existsForBookingAndReviewer(int $bookingId, int $reviewerId): bool
	{
		$sql = 'SELECT id FROM reviews WHERE booking_id = :booking_id AND reviewer_id = :reviewer_id LIMIT 1';
		$row = $this->query($sql, [
			'booking_id' => $bookingId,
			'reviewer_id' => $reviewerId,
		])->fetch();

		return is_array($row);
	}

	public function listForService(int $serviceId): array
	{
		$sql = 'SELECT r.*, reviewer.name AS reviewer_name
				FROM reviews r
				INNER JOIN users reviewer ON reviewer.id = r.reviewer_id
				WHERE r.service_id = :service_id
				ORDER BY r.id DESC';

		return $this->query($sql, ['service_id' => $serviceId])->fetchAll();
	}
}
