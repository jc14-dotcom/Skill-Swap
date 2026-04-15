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

$policy = new App\Policies\ServicePolicy();

$providerOwner = ['id' => 8, 'role' => 'provider'];
$providerOther = ['id' => 9, 'role' => 'provider'];
$admin = ['id' => 1, 'role' => 'admin'];
$seeker = ['id' => 11, 'role' => 'seeker'];
$both = ['id' => 12, 'role' => 'both'];
$guest = ['id' => 0, 'role' => 'guest'];

$service = ['id' => 44, 'user_id' => 8];

$tests = [
    'provider can create service' => $policy->canCreate($providerOwner) === true,
    'seeker can create service' => $policy->canCreate($seeker) === true,
    'both-role user can create service' => $policy->canCreate($both) === true,
    'guest cannot create service' => $policy->canCreate($guest) === false,
    'owner provider can manage own service' => $policy->canManage($providerOwner, $service) === true,
    'other provider cannot manage foreign service' => $policy->canManage($providerOther, $service) === false,
    'admin can manage any service' => $policy->canManage($admin, $service) === true,
];

$failed = array_keys(array_filter($tests, static fn(bool $ok): bool => !$ok));

if ($failed !== []) {
    echo 'ServicePolicyTest FAILED:' . PHP_EOL;
    foreach ($failed as $case) {
        echo '- ' . $case . PHP_EOL;
    }
    exit(1);
}

echo 'ServicePolicyTest PASSED' . PHP_EOL;
