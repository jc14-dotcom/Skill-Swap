<?php

declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Message extends Model
{
	protected string $table = 'messages';

	public function listConversationsForUser(int $userId): array
	{
		$sql = 'SELECT b.id AS booking_id,
					   b.status,
					   s.title AS service_title,
					   CASE WHEN b.seeker_id = :uid_case_1 THEN b.provider_id ELSE b.seeker_id END AS counterpart_id,
					   CASE WHEN b.seeker_id = :uid_case_2 THEN provider.name ELSE seeker.name END AS counterpart_name,
					   COALESCE(last_message.body, b.message, \'\') AS last_message_body,
					   COALESCE(last_message.created_at, b.updated_at) AS last_message_at,
					   (
						SELECT COUNT(*)
						FROM messages unread
						WHERE unread.booking_id = b.id
						  AND unread.receiver_id = :uid_unread
						  AND unread.read_at IS NULL
					   ) AS unread_count
				FROM bookings b
				INNER JOIN services s ON s.id = b.service_id
				INNER JOIN users seeker ON seeker.id = b.seeker_id
				INNER JOIN users provider ON provider.id = b.provider_id
				LEFT JOIN messages last_message ON last_message.id = (
					SELECT m2.id
					FROM messages m2
					WHERE m2.booking_id = b.id
					ORDER BY m2.id DESC
					LIMIT 1
				)
				WHERE b.seeker_id = :uid_where_1 OR b.provider_id = :uid_where_2
				ORDER BY last_message_at DESC, b.id DESC';

		return $this->query($sql, [
			'uid_case_1' => $userId,
			'uid_case_2' => $userId,
			'uid_unread' => $userId,
			'uid_where_1' => $userId,
			'uid_where_2' => $userId,
		])->fetchAll();
	}

	public function listThreadForBooking(int $bookingId): array
	{
		$sql = 'SELECT m.*, sender.name AS sender_name
				FROM messages m
				INNER JOIN users sender ON sender.id = m.sender_id
				WHERE m.booking_id = :booking_id
				ORDER BY m.id ASC';

		return $this->query($sql, ['booking_id' => $bookingId])->fetchAll();
	}

	public function createMessage(array $payload): int
	{
		return $this->insert([
			'booking_id' => $payload['booking_id'],
			'sender_id' => $payload['sender_id'],
			'receiver_id' => $payload['receiver_id'],
			'body' => $payload['body'],
			'read_at' => null,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	public function markThreadAsRead(int $bookingId, int $receiverId): int
	{
		$statement = $this->query(
			'UPDATE messages
			 SET read_at = :read_at,
				 updated_at = :updated_at
			 WHERE booking_id = :booking_id
			   AND receiver_id = :receiver_id
			   AND read_at IS NULL',
			[
				'read_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'booking_id' => $bookingId,
				'receiver_id' => $receiverId,
			]
		);

		return $statement->rowCount();
	}
}
