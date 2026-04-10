<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

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

foreach (glob(BASE_PATH . '/app/Helpers/*.php') ?: [] as $helperFile) {
	require_once $helperFile;
}

$app = new Core\App(BASE_PATH);
$app->run();
