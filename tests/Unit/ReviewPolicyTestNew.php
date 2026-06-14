<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Policies\ReviewPolicy;
use Tests\TestCase;

class ReviewPolicyTest extends TestCase
{
	private ReviewPolicy $policy;

	protected function setUp(): void
	{
		parent::setUp();
		$this->policy = new ReviewPolicy();
	}

	public function testSeekerCanCreateReviewForCompletedBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'completed', 'seeker_id' => 22, 'provider_id' => 10];

		$this->assertTrue($this->policy->canCreate($seeker, $booking));
	}

	public function testProviderCanCreateReviewForCompletedBooking(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'completed', 'seeker_id' => 22, 'provider_id' => 10];

		$this->assertTrue($this->policy->canCreate($provider, $booking));
	}

	public function testCannotReviewPendingBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'pending', 'seeker_id' => 22, 'provider_id' => 10];

		$this->assertFalse($this->policy->canCreate($seeker, $booking));
	}

	public function testCannotReviewAcceptedBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'accepted', 'seeker_id' => 22, 'provider_id' => 10];

		$this->assertFalse($this->policy->canCreate($seeker, $booking));
	}

	public function testNonParticipantCannotReview(): void
	{
		$outsider = ['id' => 50, 'role' => 'seeker'];
		$booking = ['status' => 'completed', 'seeker_id' => 22, 'provider_id' => 10];

		$this->assertFalse($this->policy->canCreate($outsider, $booking));
	}
}
