<?php

declare(strict_types=1);

$migrateExit = 0;
passthru('php "' . __DIR__ . '/../database/migrate.php"', $migrateExit);
if ($migrateExit !== 0) {
	exit($migrateExit);
}


$unitTests = [
    __DIR__ . '/Unit/BookingPolicyTest.php',
    __DIR__ . '/Unit/ReviewPolicyTest.php',
    __DIR__ . '/Unit/ServicePolicyTest.php',
];

foreach ($unitTests as $testFile) {
    $exitCode = 0;
    passthru('php "' . $testFile . '"', $exitCode);
    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

require __DIR__ . '/Feature/FlowRulesTest.php';

echo 'All tests PASSED' . PHP_EOL;
