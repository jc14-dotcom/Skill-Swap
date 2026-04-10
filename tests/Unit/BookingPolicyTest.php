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

$pending = ['status' => 'pending', 'provider_id' => 10, 'seeker_id' => 22];
$accepted = ['status' => 'accepted', 'provider_id' => 10, 'seeker_id' => 22];

$tests = [
    'provider can accept pending' => $policy->canTransition($provider, $pending, 'accepted') === true,
    'seeker cannot accept pending' => $policy->canTransition($seeker, $pending, 'accepted') === false,
    'seeker can cancel pending' => $policy->canTransition($seeker, $pending, 'cancelled') === true,
    'provider can complete accepted' => $policy->canTransition($provider, $accepted, 'completed') === true,
    'invalid jump is rejected' => $policy->canTransition($provider, $pending, 'completed') === false,
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
