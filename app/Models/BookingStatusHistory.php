<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class BookingStatusHistory extends Model
{
	protected string $table = 'booking_status_history';

	public function record(int $bookingId, ?string $fromStatus, string $toStatus, ?int $changedBy): int
	{
		return $this->insert([
			'booking_id' => $bookingId,
			'from_status' => $fromStatus,
			'to_status' => $toStatus,
			'changed_by' => $changedBy,
			'created_at' => date('Y-m-d H:i:s'),
		]);
	}
}
