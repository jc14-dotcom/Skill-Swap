<?php

declare(strict_types=1);

namespace Core;

class Request
{
	/** @var array<string, mixed> */
	private array $query;

	/** @var array<string, mixed> */
	private array $post;

	/** @var array<string, mixed> */
	private array $files;

	/** @var array<string, mixed> */
	private array $server;

	/** @var array<string, string> */
	private array $routeParams = [];

	public function __construct()
	{
		$this->query = $_GET;
		$this->post = $_POST;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}

	public function method(): string
	{
		$method = strtoupper((string) ($this->server['REQUEST_METHOD'] ?? 'GET'));

		if ($method === 'POST') {
			$override = strtoupper((string) ($this->post['_method'] ?? $this->server['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? ''));
			if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
				return $override;
			}
		}

		return $method;
	}

	public function path(): string
	{
		$uri = (string) ($this->server['REQUEST_URI'] ?? '/');
		$path = parse_url($uri, PHP_URL_PATH);
		$path = is_string($path) ? $path : '/';

		$trimmed = trim($path, '/');
		return $trimmed === '' ? '/' : '/' . $trimmed;
	}

	public function all(): array
	{
		return array_merge($this->query, $this->post);
	}

	public function input(string $key, mixed $default = null): mixed
	{
		$data = $this->all();
		return $data[$key] ?? $default;
	}

	public function query(string $key, mixed $default = null): mixed
	{
		return $this->query[$key] ?? $default;
	}

	public function post(string $key, mixed $default = null): mixed
	{
		return $this->post[$key] ?? $default;
	}

	public function has(string $key): bool
	{
		$value = $this->input($key);
		return $value !== null && $value !== '';
	}

	public function only(array $keys): array
	{
		$subset = [];
		foreach ($keys as $key) {
			if (array_key_exists($key, $this->all())) {
				$subset[$key] = $this->input($key);
			}
		}

		return $subset;
	}

	public function header(string $name, mixed $default = null): mixed
	{
		$header = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
		return $this->server[$header] ?? $default;
	}

	public function ip(): string
	{
		return (string) ($this->server['REMOTE_ADDR'] ?? '127.0.0.1');
	}

	public function userAgent(): string
	{
		return (string) ($this->server['HTTP_USER_AGENT'] ?? 'unknown');
	}

	public function setRouteParams(array $params): void
	{
		$this->routeParams = $params;
	}

	public function param(string $key, mixed $default = null): mixed
	{
		return $this->routeParams[$key] ?? $default;
	}

	public function params(): array
	{
		return $this->routeParams;
	}
}
