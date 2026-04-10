<?php

declare(strict_types=1);

if (!function_exists('json_response')) {
	function json_response(array $payload, int $status = 200): void
	{
		http_response_code($status);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		exit;
	}
}
