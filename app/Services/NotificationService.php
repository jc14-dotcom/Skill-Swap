<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
	private Notification $notifications;

	public function __construct()
	{
		$this->notifications = new Notification();
	}

	public function feed(array $actor, int $limit = 8): array
	{
		$userId = (int) ($actor['id'] ?? 0);
		if ($userId <= 0) {
			return [
				'unread_count' => 0,
				'items' => [],
			];
		}

		return [
			'unread_count' => $this->notifications->unreadCount($userId),
			'items' => $this->notifications->listForUser($userId, $limit),
		];
	}

	public function notify(
		int $userId,
		string $type,
		string $title,
		?string $body = null,
		?int $actorId = null,
		?string $link = null,
		array $meta = []
	): int {
		if ($userId <= 0 || trim($type) === '' || trim($title) === '') {
			throw new \InvalidArgumentException('Notification requires user, type, and title.');
		}

		$metaJson = $meta !== [] ? json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;
		if ($metaJson === false) {
			$metaJson = null;
		}

		return $this->notifications->createNotification([
			'user_id' => $userId,
			'actor_id' => $actorId,
			'type' => $type,
			'title' => $title,
			'body' => $body,
			'link' => $link,
			'meta_json' => $metaJson,
		]);
	}

	public function markAsReadForUser(array $actor, int $notificationId): bool
	{
		$userId = (int) ($actor['id'] ?? 0);
		if ($userId <= 0 || $notificationId <= 0) {
			return false;
		}

		$notification = $this->notifications->findForUser($notificationId, $userId);
		if (!is_array($notification)) {
			return false;
		}

		if (($notification['read_at'] ?? null) !== null) {
			return true;
		}

		return $this->notifications->markAsRead($notificationId, $userId);
	}

	public function markAllAsReadForUser(array $actor): int
	{
		$userId = (int) ($actor['id'] ?? 0);
		if ($userId <= 0) {
			return 0;
		}

		return $this->notifications->markAllAsRead($userId);
	}

	public function notifyBookingCreated(array $booking, int $actorId): void
	{
		$recipientId = (int) ($booking['provider_id'] ?? 0);
		if ($recipientId <= 0) {
			return;
		}

		$serviceTitle = (string) ($booking['service_title'] ?? 'your service');
		$seekerName = (string) ($booking['seeker_name'] ?? 'A member');

		$this->notify(
			$recipientId,
			'booking.created',
			'New booking request',
			$seekerName . ' requested "' . $serviceTitle . '".',
			$actorId,
			'/bookings/provider',
			[
				'booking_id' => (int) ($booking['id'] ?? 0),
				'status' => (string) ($booking['status'] ?? 'pending'),
			]
		);
	}

	public function notifyBookingTransition(array $booking, string $fromStatus, string $toStatus, int $actorId): void
	{
		$providerId = (int) ($booking['provider_id'] ?? 0);
		$seekerId = (int) ($booking['seeker_id'] ?? 0);

		$recipientId = $actorId === $providerId ? $seekerId : $providerId;
		if ($recipientId <= 0) {
			return;
		}

		$serviceTitle = (string) ($booking['service_title'] ?? 'booking');
		$link = $recipientId === $providerId ? '/bookings/provider' : '/bookings/mine';

		$this->notify(
			$recipientId,
			'booking.status_changed',
			'Booking update: ' . ucfirst($toStatus),
			'"' . $serviceTitle . '" moved from ' . $fromStatus . ' to ' . $toStatus . '.',
			$actorId,
			$link,
			[
				'booking_id' => (int) ($booking['id'] ?? 0),
				'from' => $fromStatus,
				'to' => $toStatus,
			]
		);
	}

	public function notifyReviewCreated(array $booking, int $reviewerId): void
	{
		$providerId = (int) ($booking['provider_id'] ?? 0);
		$seekerId = (int) ($booking['seeker_id'] ?? 0);

		$recipientId = $reviewerId === $providerId ? $seekerId : $providerId;
		if ($recipientId <= 0) {
			return;
		}

		$reviewerName = $reviewerId === $providerId
			? (string) ($booking['provider_name'] ?? 'The provider')
			: (string) ($booking['seeker_name'] ?? 'The seeker');

		$link = $recipientId === $providerId ? '/bookings/provider' : '/bookings/mine';
		$serviceTitle = (string) ($booking['service_title'] ?? 'booking');

		$this->notify(
			$recipientId,
			'review.created',
			'New review submitted',
			$reviewerName . ' left a review related to "' . $serviceTitle . '".',
			$reviewerId,
			$link,
			[
				'booking_id' => (int) ($booking['id'] ?? 0),
			]
		);
	}
}
