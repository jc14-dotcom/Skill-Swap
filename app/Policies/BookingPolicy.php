<?php

declare(strict_types=1);

namespace App\Policies;

class BookingPolicy
{
    public function canCreate(array $user, array $service): bool
    {
        $userId = (int) ($user['id'] ?? 0);
        if ($userId <= 0) {
            return false;
        }

        $role = (string) ($user['role'] ?? '');
        if (!in_array($role, ['seeker', 'provider', 'both', 'admin'], true)) {
            return false;
        }

        if ($role === 'admin') {
            return true;
        }

        return (int) ($service['user_id'] ?? 0) !== $userId;
    }

    public function canView(array $user, array $booking): bool
    {
        if ((string) ($user['role'] ?? '') === 'admin') {
            return true;
        }

        $userId = (int) ($user['id'] ?? 0);
        return $userId === (int) ($booking['seeker_id'] ?? 0)
            || $userId === (int) ($booking['provider_id'] ?? 0);
    }

    public function canTransition(array $user, array $booking, string $newStatus): bool
    {
        if (!self::isValidTransition((string) ($booking['status'] ?? ''), $newStatus)) {
            return false;
        }

        if ((string) ($user['role'] ?? '') === 'admin') {
            return true;
        }

        $userId = (int) ($user['id'] ?? 0);
        $providerId = (int) ($booking['provider_id'] ?? 0);
        $seekerId = (int) ($booking['seeker_id'] ?? 0);
        $currentStatus = (string) ($booking['status'] ?? '');

        if ($currentStatus === 'pending' && in_array($newStatus, ['accepted', 'rejected'], true)) {
            return $userId === $providerId;
        }

        if ($currentStatus === 'pending' && $newStatus === 'cancelled') {
            return $userId === $seekerId;
        }

        if ($currentStatus === 'accepted' && $newStatus === 'completed') {
            if (strtolower((string) ($booking['payment_status'] ?? 'unpaid')) !== 'paid') {
                return false;
            }

            return $userId === $providerId;
        }

        if ($currentStatus === 'accepted' && $newStatus === 'cancelled') {
            return $userId === $providerId || $userId === $seekerId;
        }

        return false;
    }

    public static function isValidTransition(string $from, string $to): bool
    {
        $allowed = [
            'pending' => ['accepted', 'rejected', 'cancelled'],
            'accepted' => ['completed', 'cancelled'],
        ];

        if (!array_key_exists($from, $allowed)) {
            return false;
        }

        return in_array($to, $allowed[$from], true);
    }
}