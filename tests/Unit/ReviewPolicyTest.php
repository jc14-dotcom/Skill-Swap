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

$policy = new App\Policies\ReviewPolicy();

$seeker = ['id' => 22, 'role' => 'seeker'];
$outsider = ['id' => 77, 'role' => 'seeker'];

$completed = ['status' => 'completed', 'seeker_id' => 22, 'provider_id' => 10];
$pending = ['status' => 'pending', 'seeker_id' => 22, 'provider_id' => 10];

$tests = [
    'participant can review completed booking' => $policy->canCreate($seeker, $completed) === true,
    'outsider cannot review completed booking' => $policy->canCreate($outsider, $completed) === false,
    'participant cannot review pending booking' => $policy->canCreate($seeker, $pending) === false,
];

$failed = array_keys(array_filter($tests, static fn(bool $ok): bool => !$ok));

if ($failed !== []) {
    echo 'ReviewPolicyTest FAILED:' . PHP_EOL;
    foreach ($failed as $case) {
        echo '- ' . $case . PHP_EOL;
    }
    exit(1);
}

echo 'ReviewPolicyTest PASSED' . PHP_EOL;
