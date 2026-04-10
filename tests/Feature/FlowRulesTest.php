<?php

declare(strict_types=1);

$unitTests = [
    __DIR__ . '/../Unit/BookingPolicyTest.php',
    __DIR__ . '/../Unit/ReviewPolicyTest.php',
];

foreach ($unitTests as $testFile) {
    $exitCode = 0;
    passthru('php "' . $testFile . '"', $exitCode);
    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

echo 'FlowRulesTest PASSED' . PHP_EOL;
