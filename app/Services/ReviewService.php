<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use App\Policies\ReviewPolicy;

class ReviewService
{
	private Review $reviews;
	private Booking $bookings;
	private ReviewPolicy $policy;

	public function __construct()
	{
		$this->reviews = new Review();
		$this->bookings = new Booking();
		$this->policy = new ReviewPolicy();
	}

	public function createReview(array $actor, array $payload): int
	{
		$bookingId = (int) $payload['booking_id'];
		$booking = $this->bookings->findDetailed($bookingId);

		if (!is_array($booking)) {
			throw new \RuntimeException('Booking not found.');
		}

		if (!$this->policy->canCreate($actor, $booking)) {
			throw new \RuntimeException('You cannot review this booking.');
		}

		if ($this->reviews->existsForBookingAndReviewer($bookingId, (int) $actor['id'])) {
			throw new \RuntimeException('You already submitted a review for this booking.');
		}

		$reviewerId = (int) $actor['id'];
		$revieweeId = $reviewerId === (int) $booking['seeker_id']
			? (int) $booking['provider_id']
			: (int) $booking['seeker_id'];

		return $this->reviews->createReview([
			'booking_id' => $bookingId,
			'service_id' => (int) $booking['service_id'],
			'reviewer_id' => $reviewerId,
			'reviewee_id' => $revieweeId,
			'rating' => (int) $payload['rating'],
			'comment' => $payload['comment'] ?? null,
		]);
	}

	public function listServiceReviews(int $serviceId): array
	{
		return $this->reviews->listForService($serviceId);
	}
}
