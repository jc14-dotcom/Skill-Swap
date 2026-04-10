<?php

declare(strict_types=1);

namespace App\Policies;

class ReviewPolicy
{
    public function canCreate(array $user, array $booking): bool
    {
        if ((string) ($booking['status'] ?? '') !== 'completed') {
            return false;
        }

        if ((string) ($user['role'] ?? '') === 'admin') {
            return true;
        }

        $userId = (int) ($user['id'] ?? 0);

        return $userId === (int) ($booking['seeker_id'] ?? 0)
            || $userId === (int) ($booking['provider_id'] ?? 0);
    }
}