<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Policies\BookingPolicy;

class BookingService
{
	private Booking $bookings;
	private Service $services;
	private BookingPolicy $policy;

	public function __construct()
	{
		$this->bookings = new Booking();
		$this->services = new Service();
		$this->policy = new BookingPolicy();
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

		$bookingId = $this->bookings->createBooking([
			'service_id' => $serviceId,
			'seeker_id' => (int) $actor['id'],
			'provider_id' => (int) $service['user_id'],
			'status' => 'pending',
			'message' => $payload['message'] ?? null,
			'scheduled_at' => $payload['scheduled_at'] ?? null,
		]);

		$booking = $this->bookings->findDetailed($bookingId);
		if (!is_array($booking)) {
			throw new \RuntimeException('Booking could not be loaded after creation.');
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

	public function transitionStatus(array $actor, int $bookingId, string $newStatus): array
	{
		$booking = $this->bookings->findDetailed($bookingId);
		if (!is_array($booking)) {
			throw new \RuntimeException('Booking not found.');
		}

		if (!$this->policy->canTransition($actor, $booking, $newStatus)) {
			throw new \RuntimeException('Status transition is not allowed.');
		}

		$this->bookings->updateStatus($bookingId, $newStatus);

		$updated = $this->bookings->findDetailed($bookingId);
		if (!is_array($updated)) {
			throw new \RuntimeException('Booking update failed.');
		}

		return $updated;
	}

	public function findBooking(int $bookingId): ?array
	{
		return $this->bookings->findDetailed($bookingId);
	}
}
