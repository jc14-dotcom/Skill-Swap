<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Policies\BookingPolicy;
use Tests\TestCase;

class BookingPolicyTest extends TestCase
{
	private BookingPolicy $policy;

	protected function setUp(): void
	{
		parent::setUp();
		$this->policy = new BookingPolicy();
	}

	public function testProviderCanCreateBookingForOtherService(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$service = ['user_id' => 90];

		$this->assertTrue($this->policy->canCreate($provider, $service));
	}

	public function testBothRoleUserCanCreateBooking(): void
	{
		$user = ['id' => 10, 'role' => 'both'];
		$service = ['user_id' => 90];

		$this->assertTrue($this->policy->canCreate($user, $service));
	}

	public function testProviderCannotBookOwnService(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$service = ['user_id' => 10];

		$this->assertFalse($this->policy->canCreate($provider, $service));
	}

	public function testSeekerCanCreateBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$service = ['user_id' => 90];

		$this->assertTrue($this->policy->canCreate($seeker, $service));
	}

	public function testGuestCannotCreateBooking(): void
	{
		$guest = ['id' => 0, 'role' => 'guest'];
		$service = ['user_id' => 90];

		$this->assertFalse($this->policy->canCreate($guest, $service));
	}

	public function testAdminCanCreateBooking(): void
	{
		$admin = ['id' => 1, 'role' => 'admin'];
		$service = ['user_id' => 90];

		$this->assertTrue($this->policy->canCreate($admin, $service));
	}

	public function testProviderCanAcceptPendingBooking(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($provider, $booking, 'accepted'));
	}

	public function testProviderCanRejectPendingBooking(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($provider, $booking, 'rejected'));
	}

	public function testSeekerCannotAcceptPendingBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertFalse($this->policy->canTransition($seeker, $booking, 'accepted'));
	}

	public function testSeekerCanCancelPendingBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($seeker, $booking, 'cancelled'));
	}

	public function testProviderCanCompleteAcceptedBooking(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'accepted', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($provider, $booking, 'completed'));
	}

	public function testSeekerCannotCompleteAcceptedBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'accepted', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertFalse($this->policy->canTransition($seeker, $booking, 'completed'));
	}

	public function testProviderCanCancelAcceptedBooking(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'accepted', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($provider, $booking, 'cancelled'));
	}

	public function testSeekerCanCancelAcceptedBooking(): void
	{
		$seeker = ['id' => 22, 'role' => 'seeker'];
		$booking = ['status' => 'accepted', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($seeker, $booking, 'cancelled'));
	}

	public function testInvalidTransitionIsRejected(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertFalse($this->policy->canTransition($provider, $booking, 'completed'));
	}

	public function testTerminalStatusCannotTransition(): void
	{
		$provider = ['id' => 10, 'role' => 'provider'];
		$booking = ['status' => 'completed', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertFalse($this->policy->canTransition($provider, $booking, 'accepted'));
	}

	public function testAdminCanTransition(): void
	{
		$admin = ['id' => 1, 'role' => 'admin'];
		$booking = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];

		$this->assertTrue($this->policy->canTransition($admin, $booking, 'accepted'));
	}

	public function testUserCanViewOwnBookings(): void
	{
		$user = ['id' => 10, 'role' => 'provider'];
		$booking = ['seeker_id' => 22, 'provider_id' => 10];

		$this->assertTrue($this->policy->canView($user, $booking));
	}

	public function testUserCannotViewOtherBookings(): void
	{
		$user = ['id' => 50, 'role' => 'seeker'];
		$booking = ['seeker_id' => 22, 'provider_id' => 10];

		$this->assertFalse($this->policy->canView($user, $booking));
	}

	public function testAdminCanViewAnyBooking(): void
	{
		$admin = ['id' => 1, 'role' => 'admin'];
		$booking = ['seeker_id' => 22, 'provider_id' => 10];

		$this->assertTrue($this->policy->canView($admin, $booking));
	}
}
