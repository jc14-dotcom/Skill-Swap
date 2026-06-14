<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Policies\ServicePolicy;
use Tests\TestCase;

class ServicePolicyTest extends TestCase
{
	private ServicePolicy $policy;

	protected function setUp(): void
	{
		parent::setUp();
		$this->policy = new ServicePolicy();
	}

	public function testAuthenticatedUserCanCreateService(): void
	{
		$user = ['id' => 10, 'role' => 'provider'];

		$this->assertTrue($this->policy->canCreate($user));
	}

	public function testBothRoleUserCanCreateService(): void
	{
		$user = ['id' => 10, 'role' => 'both'];

		$this->assertTrue($this->policy->canCreate($user));
	}

	public function testAdminCanCreateService(): void
	{
		$admin = ['id' => 1, 'role' => 'admin'];

		$this->assertTrue($this->policy->canCreate($admin));
	}

	public function testGuestCannotCreateService(): void
	{
		$guest = ['id' => 0, 'role' => 'guest'];

		$this->assertFalse($this->policy->canCreate($guest));
	}

	public function testOwnerCanManageService(): void
	{
		$user = ['id' => 10, 'role' => 'provider'];
		$service = ['user_id' => 10];

		$this->assertTrue($this->policy->canManage($user, $service));
	}

	public function testNonOwnerCannotManageService(): void
	{
		$user = ['id' => 20, 'role' => 'provider'];
		$service = ['user_id' => 10];

		$this->assertFalse($this->policy->canManage($user, $service));
	}

	public function testAdminCanManageAnyService(): void
	{
		$admin = ['id' => 1, 'role' => 'admin'];
		$service = ['user_id' => 10];

		$this->assertTrue($this->policy->canManage($admin, $service));
	}
}
