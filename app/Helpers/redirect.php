<?php

declare(strict_types=1);

if (!function_exists('redirect_to')) {
	function redirect_to(string $path, int $status = 302): void
	{
		header('Location: ' . $path, true, $status);
		exit;
	}
}
