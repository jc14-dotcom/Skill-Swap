<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Service;
use App\Policies\BookingPolicy;
use Core\App;
use Core\Database;

class BookingService
{
	private Booking $bookings;
	private BookingStatusHistory $statusHistory;
	private Service $services;
	private BookingPolicy $policy;
	private NotificationService $notifications;

	public function __construct()
	{
		$this->bookings = new Booking();
		$this->statusHistory = new BookingStatusHistory();
		$this->services = new Service();
		$this->policy = new BookingPolicy();
		$this->notifications = new NotificationService();
	}

	public function createBooking(array $actor, array $payload): array
	{
		$serviceId = (int) $payload['service_id'];
		$service = $this->services->findWithProvider($serviceId);

		if (!is_array($service)) {
			throw new \RuntimeException('Service not found.');
		}

		if (!$this->policy->canCreate($actor, $service)) {
			throw new \RuntimeException('You are not allowed to book this service.');
		}

		$bookingId = Database::transaction(function () use ($actor, $payload, $service, $serviceId): int {
			$createdId = $this->bookings->createBooking([
				'service_id' => $serviceId,
				'seeker_id' => (int) $actor['id'],
				'provider_id' => (int) $service['user_id'],
				'status' => 'pending',
				'message' => $payload['message'] ?? null,
				'scheduled_at' => $payload['scheduled_at'] ?? null,
			]);

			$this->statusHistory->record($createdId, null, 'pending', (int) ($actor['id'] ?? 0));
			return $createdId;
		});

		$booking = $this->bookings->findDetailed((int) $bookingId);
		if (!is_array($booking)) {
			throw new \RuntimeException('Booking could not be loaded after creation.');
		}

		App::logger()->info('booking.created', [
			'booking_id' => (int) $booking['id'],
			'service_id' => (int) $booking['service_id'],
			'seeker_id' => (int) $booking['seeker_id'],
			'provider_id' => (int) $booking['provider_id'],
			'status' => (string) $booking['status'],
		]);

		try {
			$this->notifications->notifyBookingCreated($booking, (int) ($actor['id'] ?? 0));
		} catch (\Throwable $exception) {
			App::logger()->warning('notification.booking_created.failed', [
				'booking_id' => (int) $booking['id'],
				'error' => $exception->getMessage(),
			]);
		}

		return $booking;
	}

	public function listForSeeker(array $actor): array
	{
		return $this->bookings->listForSeeker((int) $actor['id']);
	}

	public function listForProvider(array $actor): array
	{
		return $this->bookings->listForProvider((int) $actor['id']);
	}

	public function listForProviderService(array $actor, array $service): array
	{
		$serviceId = (int) ($service['id'] ?? 0);
		$providerId = (int) ($service['user_id'] ?? 0);
		$actorId = (int) ($actor['id'] ?? 0);
		$actorRole = (string) ($actor['role'] ?? '');

		if ($serviceId <= 0 || $providerId <= 0) {
			return [];
		}

		if ($actorRole !== 'admin' && $actorId !== $providerId) {
			throw new \RuntimeException('You are not allowed to view bookings for this service.');
		}

		return $this->bookings->listForProviderService($providerId, $serviceId);
	}

	public function transitionStatus(array $actor, int $bookingId, string $newStatus): array
	{
		$booking = $this->bookings->findDetailed($bookingId);
		if (!is_array($booking)) {
			throw new \RuntimeException('Booking not found.');
		}

		if (!$this->policy->canTransition($actor, $booking, $newStatus)) {
			throw new \RuntimeException('Status transition is not allowed.');
		}

		$currentStatus = (string) ($booking['status'] ?? '');

		Database::transaction(function () use ($actor, $bookingId, $currentStatus, $newStatus): void {
			$this->bookings->updateStatus($bookingId, $newStatus);
			$this->statusHistory->record($bookingId, $currentStatus, $newStatus, (int) ($actor['id'] ?? 0));
		});

		$updated = $this->bookings->findDetailed($bookingId);
		if (!is_array($updated)) {
			throw new \RuntimeException('Booking update failed.');
		}

		App::logger()->info('booking.status.transitioned', [
			'booking_id' => (int) $updated['id'],
			'from_status' => $currentStatus,
			'to_status' => (string) $updated['status'],
			'actor_id' => (int) ($actor['id'] ?? 0),
		]);

		try {
			$this->notifications->notifyBookingTransition(
				$updated,
				$currentStatus,
				(string) $updated['status'],
				(int) ($actor['id'] ?? 0)
			);
		} catch (\Throwable $exception) {
			App::logger()->warning('notification.booking_transition.failed', [
				'booking_id' => (int) $updated['id'],
				'from_status' => $currentStatus,
				'to_status' => (string) $updated['status'],
				'error' => $exception->getMessage(),
			]);
		}

		return $updated;
	}

	public function findBooking(int $bookingId): ?array
	{
		return $this->bookings->findDetailed($bookingId);
	}
}
