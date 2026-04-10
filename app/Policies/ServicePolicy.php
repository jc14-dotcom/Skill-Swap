<?php

declare(strict_types=1);

namespace App\Policies;

class ServicePolicy
{
    public function canCreate(array $user): bool
    {
        $role = (string) ($user['role'] ?? 'seeker');
        return in_array($role, ['provider', 'admin'], true);
    }

    public function canManage(array $user, array $service): bool
    {
        if ((string) ($user['role'] ?? '') === 'admin') {
            return true;
        }

        return (int) ($service['user_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }
}