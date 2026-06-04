<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RevenueTransaction;

class RevenueService
{
	private RevenueTransaction $transactions;
	private float $commissionRate;

	public function __construct()
	{
		$this->transactions = new RevenueTransaction();
		$this->commissionRate = 0.10;
	}

	public function recordCommission(array $booking, ?float $rate = null): ?int
	{
		$bookingId = (int) ($booking['id'] ?? 0);
		if ($bookingId <= 0) {
			return null;
		}

		$existing = $this->transactions->findForBooking($bookingId, 'commission');
		if (is_array($existing)) {
			return null;
		}

		$commissionRate = $rate ?? $this->commissionRate;
		$price = (float) ($booking['service_price'] ?? 0);
		if ($price <= 0) {
			$price = (float) ($booking['service_current_price'] ?? 0);
		}

		if ($price <= 0) {
			return null;
		}

		$commission = round($price * $commissionRate, 2);
		if ($commission <= 0) {
			return null;
		}

		return $this->transactions->createTransaction([
			'booking_id' => $bookingId,
			'service_id' => (int) ($booking['service_id'] ?? 0) ?: null,
			'type' => 'commission',
			'amount' => $commission,
			'description' => 'Commission ' . (int) ($commissionRate * 100) . '%',
		]);
	}

	public function recordBoost(int $serviceId, int $days, float $amount, array $actor, string $paymentMethod, ?string $boostExpiresAt = null): array
	{
		$reference = 'BST-' . strtoupper(bin2hex(random_bytes(4)));
		$description = 'Boost ' . $days . ' days';
		$transactionId = $this->transactions->createTransaction([
			'booking_id' => null,
			'service_id' => $serviceId > 0 ? $serviceId : null,
			'type' => 'boost',
			'amount' => $amount,
			'description' => $description,
		]);

		return [
			'transaction_id' => $transactionId,
			'reference' => $reference,
			'amount' => $amount,
			'days' => $days,
			'description' => $description,
			'payment_method' => $paymentMethod,
			'boost_expires_at' => $boostExpiresAt,
			'created_at' => date('Y-m-d H:i:s'),
			'actor_name' => (string) ($actor['name'] ?? 'Provider'),
		];
	}

	public function buildBoostReceipt(array $service): array
	{
		return [
			'reference' => 'BST-DEMO',
			'amount' => 0,
			'days' => null,
			'description' => 'Boost activated',
			'payment_method' => 'Demo',
			'boost_expires_at' => $service['boost_expires_at'] ?? null,
			'created_at' => date('Y-m-d H:i:s'),
			'actor_name' => (string) ($service['provider_name'] ?? 'Provider'),
		];
	}
}
