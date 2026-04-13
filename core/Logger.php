<?php

declare(strict_types=1);

namespace Core;

class Logger
{
	private string $path;
	private string $level;

	/** @var array<string, int> */
	private array $levels = [
		'debug' => 100,
		'info' => 200,
		'warning' => 300,
		'error' => 400,
	];

	public function __construct(string $basePath, array $config = [])
	{
		$defaultPath = rtrim($basePath, '/\\') . '/storage/logs/app.log';
		$configuredPath = (string) ($config['path'] ?? $defaultPath);
		$this->path = $configuredPath !== '' ? $configuredPath : $defaultPath;

		$configuredLevel = strtolower((string) ($config['level'] ?? 'debug'));
		$this->level = array_key_exists($configuredLevel, $this->levels) ? $configuredLevel : 'debug';
	}

	public function debug(string $message, array $context = []): void
	{
		$this->log('debug', $message, $context);
	}

	public function info(string $message, array $context = []): void
	{
		$this->log('info', $message, $context);
	}

	public function warning(string $message, array $context = []): void
	{
		$this->log('warning', $message, $context);
	}

	public function error(string $message, array $context = []): void
	{
		$this->log('error', $message, $context);
	}

	public function log(string $level, string $message, array $context = []): void
	{
		$normalizedLevel = strtolower($level);
		if (!array_key_exists($normalizedLevel, $this->levels)) {
			$normalizedLevel = 'debug';
		}

		if (!$this->shouldLog($normalizedLevel)) {
			return;
		}

		$dir = dirname($this->path);
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		$requestContext = $this->resolveRequestContext();

		$payload = [
			'timestamp' => date('Y-m-d H:i:s'),
			'level' => $normalizedLevel,
			'message' => $message,
		];

		if (is_string($requestContext['request_id'])) {
			$payload['request_id'] = $requestContext['request_id'];
		}

		if (
			is_string($requestContext['method'])
			|| is_string($requestContext['path'])
			|| is_string($requestContext['ip'])
		) {
			$requestPayload = [];
			if (is_string($requestContext['method'])) {
				$requestPayload['method'] = $requestContext['method'];
			}
			if (is_string($requestContext['path'])) {
				$requestPayload['path'] = $requestContext['path'];
			}
			if (is_string($requestContext['ip'])) {
				$requestPayload['ip'] = $requestContext['ip'];
			}

			$payload['request'] = $requestPayload;
		}

		$payload['context'] = $this->normalizeContext($context);

		$line = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		if (!is_string($line)) {
			return;
		}

		file_put_contents($this->path, $line . PHP_EOL, FILE_APPEND);
	}

	private function shouldLog(string $level): bool
	{
		return $this->levels[$level] >= $this->levels[$this->level];
	}

	private function normalizeContext(array $context): array
	{
		$normalized = [];
		foreach ($context as $key => $value) {
			if (is_scalar($value) || $value === null) {
				$normalized[$key] = $value;
				continue;
			}

			if (is_array($value)) {
				$normalized[$key] = $value;
				continue;
			}

			if ($value instanceof \Throwable) {
				$normalized[$key] = [
					'class' => $value::class,
					'message' => $value->getMessage(),
					'file' => $value->getFile(),
					'line' => $value->getLine(),
				];
				continue;
			}

			$normalized[$key] = (string) $value;
		}

		return $normalized;
	}

	/** @return array{request_id:?string,method:?string,path:?string,ip:?string} */
	private function resolveRequestContext(): array
	{
		$requestId = trim((string) ($_SERVER['APP_REQUEST_ID'] ?? $_SERVER['HTTP_X_REQUEST_ID'] ?? ''));
		$method = trim((string) ($_SERVER['REQUEST_METHOD'] ?? ''));
		$path = trim((string) ($_SERVER['REQUEST_URI'] ?? ''));
		$ip = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

		return [
			'request_id' => $requestId !== '' ? $requestId : null,
			'method' => $method !== '' ? $method : null,
			'path' => $path !== '' ? $path : null,
			'ip' => $ip !== '' ? $ip : null,
		];
	}
}
