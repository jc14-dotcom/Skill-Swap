<?php

declare(strict_types=1);

namespace Core;

use ErrorException;
use Throwable;

class App
{
	private static ?self $instance = null;

	private string $basePath;

	/** @var array<string, mixed> */
	private array $config = [];

	private Router $router;

	public function __construct(string $basePath)
	{
		self::$instance = $this;
		$this->basePath = rtrim($basePath, '/\\');

		$this->loadEnvironment();
		$this->loadConfig();

		date_default_timezone_set((string) $this->config('app.timezone', 'UTC'));

		$this->registerErrorHandling();

		Session::start((array) $this->config('app.session', []));
		Database::connect((array) $this->config('database', []));

		$this->router = new Router();
		$this->router->setMiddlewareAliases((array) $this->config('app.middleware_aliases', []));
	}

	public static function instance(): self
	{
		if (!self::$instance instanceof self) {
			throw new \RuntimeException('Application has not been bootstrapped.');
		}

		return self::$instance;
	}

	public static function getConfig(string $key, mixed $default = null): mixed
	{
		return self::instance()->config($key, $default);
	}

	public function config(string $key, mixed $default = null): mixed
	{
		$segments = explode('.', $key);
		$value = $this->config;

		foreach ($segments as $segment) {
			if (!is_array($value) || !array_key_exists($segment, $value)) {
				return $default;
			}

			$value = $value[$segment];
		}

		return $value;
	}

	public function basePath(string $path = ''): string
	{
		if ($path === '') {
			return $this->basePath;
		}

		return $this->basePath . '/' . ltrim($path, '/');
	}

	public function run(): void
	{
		$request = new Request();
		$response = new Response($this->basePath);

		$router = $this->router;
		require $this->basePath('routes/web.php');

		$this->router->dispatch($request, $response);
	}

	private function loadEnvironment(): void
	{
		$envPath = $this->basePath('.env');
		if (!is_file($envPath)) {
			return;
		}

		$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (!is_array($lines)) {
			return;
		}

		foreach ($lines as $line) {
			$line = trim($line);

			if ($line === '' || str_starts_with($line, '#')) {
				continue;
			}

			if (!str_contains($line, '=')) {
				continue;
			}

			[$key, $value] = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);

			if ($value !== '' && (
				(str_starts_with($value, '"') && str_ends_with($value, '"')) ||
				(str_starts_with($value, "'") && str_ends_with($value, "'"))
			)) {
				$value = substr($value, 1, -1);
			}

			putenv($key . '=' . $value);
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
		}
	}

	private function loadConfig(): void
	{
		$configDir = $this->basePath('config');
		foreach (glob($configDir . '/*.php') ?: [] as $configFile) {
			$key = pathinfo($configFile, PATHINFO_FILENAME);
			$data = require $configFile;
			$this->config[$key] = is_array($data) ? $data : [];
		}
	}

	private function registerErrorHandling(): void
	{
		set_error_handler(static function (
			int $severity,
			string $message,
			string $file,
			int $line
		): bool {
			throw new ErrorException($message, 0, $severity, $file, $line);
		});

		set_exception_handler(function (Throwable $exception): void {
			$this->logException($exception);

			http_response_code(500);
			$isDebug = (bool) $this->config('app.debug', false);

			$errorMessage = $isDebug
				? $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine()
				: 'Something went wrong. Please try again later.';

			$errorView = $this->basePath('app/Views/errors/500.php');
			if (is_file($errorView)) {
				include $errorView;
				return;
			}

			echo $errorMessage;
		});
	}

	private function logException(Throwable $exception): void
	{
		$logDir = $this->basePath('storage/logs');
		if (!is_dir($logDir)) {
			mkdir($logDir, 0777, true);
		}

		$logFile = $logDir . '/app.log';
		$line = sprintf(
			"[%s] %s: %s in %s:%d\n%s\n\n",
			date('Y-m-d H:i:s'),
			$exception::class,
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString()
		);

		file_put_contents($logFile, $line, FILE_APPEND);
	}
}
