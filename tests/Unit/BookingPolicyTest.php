<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Core\\' => BASE_PATH . '/core/',
        'App\\' => BASE_PATH . '/app/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relative = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
});

$policy = new App\Policies\BookingPolicy();

$provider = ['id' => 10, 'role' => 'provider'];
$seeker = ['id' => 22, 'role' => 'seeker'];
$bothProvider = ['id' => 10, 'role' => 'both'];
$bothSeeker = ['id' => 22, 'role' => 'both'];
$guest = ['id' => 0, 'role' => 'guest'];

$serviceByOther = ['user_id' => 90];
$serviceOwnedByProvider = ['user_id' => 10];

$pending = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];
$accepted = ['status' => 'accepted', 'provider_id' => 10, 'seeker_id' => 22];
$completed = ['status' => 'completed', 'provider_id' => 10, 'seeker_id' => 22];

$tests = [
    'provider can create booking for another member service' => $policy->canCreate($provider, $serviceByOther) === true,
    'both-role user can create booking for another member service' => $policy->canCreate($bothProvider, $serviceByOther) === true,
    'provider cannot book own service' => $policy->canCreate($provider, $serviceOwnedByProvider) === false,
    'seeker can create booking' => $policy->canCreate($seeker, $serviceByOther) === true,
    'guest cannot create booking' => $policy->canCreate($guest, $serviceByOther) === false,
    'provider can accept pending' => $policy->canTransition($provider, $pending, 'accepted') === true,
    'provider can reject pending' => $policy->canTransition($provider, $pending, 'rejected') === true,
    'seeker cannot accept pending' => $policy->canTransition($seeker, $pending, 'accepted') === false,
    'seeker can cancel pending' => $policy->canTransition($seeker, $pending, 'cancelled') === true,
    'provider can complete accepted' => $policy->canTransition($provider, $accepted, 'completed') === true,
    'both-role provider can complete accepted' => $policy->canTransition($bothProvider, $accepted, 'completed') === true,
    'provider can cancel accepted' => $policy->canTransition($provider, $accepted, 'cancelled') === true,
    'both-role seeker can cancel accepted' => $policy->canTransition($bothSeeker, $accepted, 'cancelled') === true,
    'seeker can cancel accepted' => $policy->canTransition($seeker, $accepted, 'cancelled') === true,
    'seeker cannot complete accepted' => $policy->canTransition($seeker, $accepted, 'completed') === false,
    'invalid jump is rejected' => $policy->canTransition($provider, $pending, 'completed') === false,
    'terminal status cannot transition' => $policy->canTransition($provider, $completed, 'accepted') === false,
];

$failed = array_keys(array_filter($tests, static fn(bool $ok): bool => !$ok));

if ($failed !== []) {
    echo 'BookingPolicyTest FAILED:' . PHP_EOL;
    foreach ($failed as $case) {
        echo '- ' . $case . PHP_EOL;
    }
    exit(1);
}

echo 'BookingPolicyTest PASSED' . PHP_EOL;
