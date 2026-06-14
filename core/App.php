<?php

declare(strict_types=1);

namespace Core;

use ErrorException;
use Core\Exceptions\AppException;
use Throwable;

class App
{
	private static ?self $instance = null;

	private string $basePath;

	/** @var array<string, mixed> */
	private array $config = [];

	private Logger $logger;
	private string $requestId;

	private Router $router;
	private Container $container;

	public function __construct(string $basePath)
	{
		self::$instance = $this;
		$this->basePath = rtrim($basePath, '/\\');

		$this->loadEnvironment();
		$this->loadConfig();
		$this->requestId = $this->resolveRequestId();
		$_SERVER['APP_REQUEST_ID'] = $this->requestId;
		$_ENV['APP_REQUEST_ID'] = $this->requestId;
		$this->logger = new Logger($this->basePath, (array) $this->config('logging', []));

		date_default_timezone_set((string) $this->config('app.timezone', 'UTC'));

		$this->registerErrorHandling();

		Session::start((array) $this->config('app.session', []));
		Database::connect((array) $this->config('database', []));

		$this->container = new Container();
		$this->registerContainerBindings();

		$this->router = new Router($this->container);
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

	public static function logger(): Logger
	{
		return self::instance()->logger;
	}

	public static function container(): Container
	{
		return self::instance()->container;
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

		$apiFile = $this->basePath('routes/api.php');
		$webFile = $this->basePath('routes/web.php');

		if (str_starts_with($request->path(), '/api')) {
			if (is_file($apiFile)) {
				require $apiFile;
			}
		} else {
			if (is_file($webFile)) {
				require $webFile;
			}
		}

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

	private function registerContainerBindings(): void
	{
		$this->container->instance(App::class, $this);
		$this->container->instance(Logger::class, $this->logger);
		$this->container->instance(Container::class, $this->container);

		$this->container->singleton(Request::class, static fn(): Request => new Request());
		$this->container->singleton(Response::class, static fn(): Response => new Response(self::instance()->basePath));
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
			$actor = Session::get('auth.user');
			$actorId = is_array($actor) ? (int) ($actor['id'] ?? 0) : 0;

			$statusCode = $exception instanceof AppException ? $exception->getStatusCode() : 500;

			$this->logger->error('Unhandled exception', [
				'request_id' => $this->requestId,
				'method' => (string) ($_SERVER['REQUEST_METHOD'] ?? 'CLI'),
				'path' => (string) ($_SERVER['REQUEST_URI'] ?? '/'),
				'actor_id' => $actorId > 0 ? $actorId : null,
				'status_code' => $statusCode,
				'exception' => $exception,
				'trace' => $exception->getTraceAsString(),
			]);

			http_response_code($statusCode);
			$isDebug = (bool) $this->config('app.debug', false);

			$errorMessage = $isDebug
				? $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine()
				: 'Something went wrong. Please try again later.';

			$errorView = $this->basePath('app/Views/errors/' . $statusCode . '.php');
			if (is_file($errorView)) {
				include $errorView;
				return;
			}

			$errorView = $this->basePath('app/Views/errors/500.php');
			if (is_file($errorView)) {
				include $errorView;
				return;
			}

			echo $errorMessage;
		});
	}

	private function resolveRequestId(): string
	{
		$incoming = (string) ($_SERVER['HTTP_X_REQUEST_ID'] ?? '');
		if ($incoming !== '' && preg_match('/^[a-zA-Z0-9\-_]{8,100}$/', $incoming) === 1) {
			return $incoming;
		}

		try {
			return bin2hex(random_bytes(8));
		} catch (\Throwable $exception) {
			return uniqid('req_', true);
		}
	}
}
