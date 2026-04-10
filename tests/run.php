<?php

declare(strict_types=1);

$tests = [
    __DIR__ . '/Unit/BookingPolicyTest.php',
    __DIR__ . '/Unit/ReviewPolicyTest.php',
    __DIR__ . '/Unit/ServicePolicyTest.php',
    __DIR__ . '/Feature/FlowRulesTest.php',
];

foreach ($tests as $testFile) {
    $exitCode = 0;
    passthru('php "' . $testFile . '"', $exitCode);
    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

echo 'All tests PASSED' . PHP_EOL;
