<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Service;
use App\Policies\ServicePolicy;

class ServiceService
{
	private Service $services;
	private ServicePolicy $policy;

	public function __construct(?Service $services = null, ?ServicePolicy $policy = null)
	{
		$this->services = $services ?? new Service();
		$this->policy = $policy ?? new ServicePolicy();
	}

	public function listPublicServices(string $searchQuery = ''): array
	{
		return $this->services->listPublic($searchQuery);
	}

	public function boostPlans(): array
	{
		return [
			3 => [
				'days' => 3,
				'price' => 50.00,
				'label' => '3 days',
			],
			7 => [
				'days' => 7,
				'price' => 100.00,
				'label' => '7 days',
			],
			15 => [
				'days' => 15,
				'price' => 200.00,
				'label' => '15 days',
			],
		];
	}

	public function getBoostPlan(int $days): array
	{
		$plans = $this->boostPlans();
		if (!isset($plans[$days])) {
			throw new \RuntimeException('Selected boost plan is not available.');
		}

		return $plans[$days];
	}

	public function findService(int $id): ?array
	{
		return $this->services->findWithProvider($id);
	}

	public function createService(array $actor, array $payload): int
	{
		if (!$this->policy->canCreate($actor)) {
			throw new \RuntimeException('Only authenticated members can create services.');
		}

		return $this->services->createService([
			'user_id' => (int) $actor['id'],
			'title' => (string) $payload['title'],
			'description' => (string) $payload['description'],
			'price' => (float) $payload['price'],
			'status' => 'active',
		]);
	}

	public function listOwnServices(array $actor): array
	{
		return $this->services->listByProvider((int) $actor['id']);
	}

	public function canManageService(?array $actor, array $service): bool
	{
		if (!is_array($actor)) {
			return false;
		}

		return $this->policy->canManage($actor, $service);
	}

	public function findManagedService(array $actor, int $serviceId): array
	{
		$service = $this->services->findWithProvider($serviceId);

		if (!is_array($service)) {
			throw new \RuntimeException('Service not found.');
		}

		if (!$this->policy->canManage($actor, $service)) {
			throw new \RuntimeException('You are not allowed to manage this service.');
		}

		return $service;
	}

	public function updateService(array $actor, int $serviceId, array $payload): bool
	{
		$service = $this->findManagedService($actor, $serviceId);

		$updated = $this->services->updateService($serviceId, [
			'title' => (string) $payload['title'],
			'description' => (string) $payload['description'],
			'price' => (float) $payload['price'],
			'status' => (string) $payload['status'],
		]);

		if (!$updated) {
			throw new \RuntimeException('Service update failed.');
		}

		return true;
	}

	public function deleteService(array $actor, int $serviceId): bool
	{
		$service = $this->findManagedService($actor, $serviceId);

		if ($this->services->hasBookings((int) $service['id'])) {
			throw new \RuntimeException('Cannot delete a service that already has bookings.');
		}

		$deleted = $this->services->deleteService((int) $service['id']);

		if (!$deleted) {
			throw new \RuntimeException('Service delete failed.');
		}

		return true;
	}

	public function boostService(array $actor, int $serviceId, int $days): array
	{
		$service = $this->findManagedService($actor, $serviceId);
		$plan = $this->getBoostPlan($days);

		$now = new \DateTimeImmutable('now');
		$expiresAt = $now->modify('+' . (int) $plan['days'] . ' days');

		$updated = $this->services->updateBoost($serviceId, [
			'is_boosted' => 1,
			'boost_started_at' => $now->format('Y-m-d H:i:s'),
			'boost_expires_at' => $expiresAt->format('Y-m-d H:i:s'),
		]);

		if (!$updated) {
			throw new \RuntimeException('Boost update failed.');
		}

		$service = $this->services->findWithProvider($serviceId);
		if (!is_array($service)) {
			throw new \RuntimeException('Service not found after boost update.');
		}

		return $service;
	}
}
