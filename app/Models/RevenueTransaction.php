<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class RevenueTransaction extends Model
{
	protected string $table = 'revenue_transactions';

	public function createTransaction(array $payload): int
	{
		return $this->insert([
			'booking_id' => $payload['booking_id'] ?? null,
			'service_id' => $payload['service_id'] ?? null,
			'type' => $payload['type'],
			'amount' => $payload['amount'],
			'description' => $payload['description'] ?? null,
			'created_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function findForBooking(int $bookingId, string $type): ?array
	{
		$sql = 'SELECT * FROM revenue_transactions WHERE booking_id = :booking_id AND type = :type LIMIT 1';
		$row = $this->query($sql, [
			'booking_id' => $bookingId,
			'type' => $type,
		])->fetch();

		return is_array($row) ? $row : null;
	}

	public function findForService(int $serviceId, string $type): ?array
	{
		$sql = 'SELECT * FROM revenue_transactions WHERE service_id = :service_id AND type = :type ORDER BY id DESC LIMIT 1';
		$row = $this->query($sql, [
			'service_id' => $serviceId,
			'type' => $type,
		])->fetch();

		return is_array($row) ? $row : null;
	}
}
