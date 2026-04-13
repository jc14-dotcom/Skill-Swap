<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Message;
use App\Policies\BookingPolicy;
use Core\App;

class MessageService
{
	private Message $messages;
	private Booking $bookings;
	private BookingPolicy $policy;
	private NotificationService $notifications;

	public function __construct()
	{
		$this->messages = new Message();
		$this->bookings = new Booking();
		$this->policy = new BookingPolicy();
		$this->notifications = new NotificationService();
	}

	public function listConversations(array $actor): array
	{
		$userId = (int) ($actor['id'] ?? 0);
		if ($userId <= 0) {
			return [];
		}

		return $this->messages->listConversationsForUser($userId);
	}

	public function openThread(array $actor, int $bookingId): array
	{
		$booking = $this->bookings->findDetailed($bookingId);
		if (!is_array($booking)) {
			throw new \RuntimeException('Booking not found for this conversation.');
		}

		if (!$this->policy->canView($actor, $booking)) {
			throw new \RuntimeException('You are not allowed to access this conversation.');
		}

		$this->messages->markThreadAsRead($bookingId, (int) ($actor['id'] ?? 0));

		$actorId = (int) ($actor['id'] ?? 0);
		$counterpartId = $actorId === (int) $booking['seeker_id']
			? (int) $booking['provider_id']
			: (int) $booking['seeker_id'];

		$counterpartName = $counterpartId === (int) $booking['provider_id']
			? (string) ($booking['provider_name'] ?? 'Member')
			: (string) ($booking['seeker_name'] ?? 'Member');

		return [
			'booking' => $booking,
			'counterpart' => [
				'id' => $counterpartId,
				'name' => $counterpartName,
			],
			'messages' => $this->messages->listThreadForBooking($bookingId),
		];
	}

	public function sendMessage(array $actor, int $bookingId, string $body): array
	{
		$booking = $this->bookings->findDetailed($bookingId);
		if (!is_array($booking)) {
			throw new \RuntimeException('Booking not found for this conversation.');
		}

		if (!$this->policy->canView($actor, $booking)) {
			throw new \RuntimeException('You are not allowed to post in this conversation.');
		}

		$senderId = (int) ($actor['id'] ?? 0);
		if ($senderId <= 0) {
			throw new \RuntimeException('Invalid sender.');
		}

		$cleanBody = trim($body);
		if ($cleanBody === '') {
			throw new \RuntimeException('Message body is required.');
		}

		$receiverId = $senderId === (int) $booking['seeker_id']
			? (int) $booking['provider_id']
			: (int) $booking['seeker_id'];

		$messageId = $this->messages->createMessage([
			'booking_id' => $bookingId,
			'sender_id' => $senderId,
			'receiver_id' => $receiverId,
			'body' => $cleanBody,
		]);

		$message = $this->messages->find($messageId);
		if (!is_array($message)) {
			throw new \RuntimeException('Message could not be loaded after send.');
		}

		try {
			$snippet = substr($cleanBody, 0, 120);
			if (strlen($cleanBody) > 120) {
				$snippet .= '...';
			}

			$this->notifications->notify(
				$receiverId,
				'message.received',
				'New message received',
				$snippet,
				$senderId,
				'/messages/' . $bookingId,
				[
					'booking_id' => $bookingId,
					'message_id' => $messageId,
				]
			);
		} catch (\Throwable $exception) {
			App::logger()->warning('notification.message_received.failed', [
				'booking_id' => $bookingId,
				'message_id' => $messageId,
				'error' => $exception->getMessage(),
			]);
		}

		return $message;
	}
}
