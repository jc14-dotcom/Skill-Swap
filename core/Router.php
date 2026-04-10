<?php

declare(strict_types=1);

namespace Core;

class Router
{
	/** @var array<string, array<int, array<string, mixed>>> */
	private array $routes = [];

	/** @var array<string, string> */
	private array $middlewareAliases = [];

	public function setMiddlewareAliases(array $aliases): void
	{
		$this->middlewareAliases = $aliases;
	}

	public function get(string $path, mixed $handler, array $options = []): self
	{
		return $this->add('GET', $path, $handler, $options);
	}

	public function post(string $path, mixed $handler, array $options = []): self
	{
		return $this->add('POST', $path, $handler, $options);
	}

	public function put(string $path, mixed $handler, array $options = []): self
	{
		return $this->add('PUT', $path, $handler, $options);
	}

	public function patch(string $path, mixed $handler, array $options = []): self
	{
		return $this->add('PATCH', $path, $handler, $options);
	}

	public function delete(string $path, mixed $handler, array $options = []): self
	{
		return $this->add('DELETE', $path, $handler, $options);
	}

	public function add(string $method, string $path, mixed $handler, array $options = []): self
	{
		$normalizedPath = $this->normalizePath($path);
		[$regex, $paramNames] = $this->compilePath($normalizedPath);

		$this->routes[strtoupper($method)][] = [
			'path' => $normalizedPath,
			'regex' => $regex,
			'paramNames' => $paramNames,
			'handler' => $handler,
			'middleware' => $options['middleware'] ?? [],
		];

		return $this;
	}

	public function dispatch(Request $request, Response $response): void
	{
		$method = $request->method();
		$path = $request->path();

		$match = $this->match($method, $path);
		if ($match === null) {
			$this->render404();
			return;
		}

		$request->setRouteParams($match['params']);

		$route = $match['route'];
		$middlewareStack = $route['middleware'];

		$controllerAction = function () use ($route, $request, $response, $match) {
			return $this->invokeHandler($route['handler'], $request, $response, $match['params']);
		};

		$pipeline = array_reduce(
			array_reverse($middlewareStack),
			fn(callable $next, string $middleware) => fn() => $this->invokeMiddleware($middleware, $request, $next),
			$controllerAction
		);

		$result = $pipeline();
		if (is_string($result)) {
			echo $result;
		}
	}

	private function match(string $method, string $path): ?array
	{
		foreach ($this->routes[$method] ?? [] as $route) {
			if (!preg_match($route['regex'], $path, $matches)) {
				continue;
			}

			$params = [];
			foreach ($route['paramNames'] as $name) {
				$params[$name] = (string) ($matches[$name] ?? '');
			}

			return [
				'route' => $route,
				'params' => $params,
			];
		}

		return null;
	}

	private function invokeHandler(mixed $handler, Request $request, Response $response, array $params): mixed
	{
		if (is_callable($handler)) {
			return $handler($request, $response);
		}

		if (is_string($handler) && str_contains($handler, '@')) {
			[$controller, $action] = explode('@', $handler, 2);
			$handler = [$controller, $action];
		}

		if (is_array($handler) && count($handler) === 2) {
			[$controllerClass, $action] = $handler;

			if (!is_string($controllerClass) || !is_string($action)) {
				throw new \RuntimeException('Invalid route handler provided.');
			}

			if (!class_exists($controllerClass)) {
				throw new \RuntimeException('Controller does not exist: ' . $controllerClass);
			}

			$controller = new $controllerClass($request, $response);

			if (!method_exists($controller, $action)) {
				throw new \RuntimeException('Action does not exist: ' . $controllerClass . '::' . $action);
			}

			return $controller->{$action}(...array_values($params));
		}

		throw new \RuntimeException('Unsupported route handler type.');
	}

	private function invokeMiddleware(string $spec, Request $request, callable $next): mixed
	{
		[$name, $rawParams] = array_pad(explode(':', $spec, 2), 2, null);
		$params = $rawParams !== null && $rawParams !== '' ? array_map('trim', explode(',', $rawParams)) : [];

		$class = $this->middlewareAliases[$name] ?? $name;

		if (!class_exists($class)) {
			throw new \RuntimeException('Middleware does not exist: ' . $class);
		}

		$middleware = new $class();

		if (!method_exists($middleware, 'handle')) {
			throw new \RuntimeException('Middleware handle method missing: ' . $class);
		}

		return $middleware->handle($request, $next, ...$params);
	}

	private function normalizePath(string $path): string
	{
		$trimmed = trim($path, '/');
		return $trimmed === '' ? '/' : '/' . $trimmed;
	}

	/** @return array{0: string, 1: array<int, string>} */
	private function compilePath(string $path): array
	{
		if ($path === '/') {
			return ['#^/$#', []];
		}

		$paramNames = [];
		$segments = explode('/', trim($path, '/'));
		$regexSegments = [];

		foreach ($segments as $segment) {
			if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $match)) {
				$paramNames[] = $match[1];
				$regexSegments[] = '(?P<' . $match[1] . '>[^/]+)';
				continue;
			}

			$regexSegments[] = preg_quote($segment, '#');
		}

		$regex = '#^/' . implode('/', $regexSegments) . '$#';
		return [$regex, $paramNames];
	}

	private function render404(): void
	{
		http_response_code(404);
		$file = BASE_PATH . '/app/Views/errors/404.php';
		if (is_file($file)) {
			include $file;
			return;
		}

		echo '404 Not Found';
	}
}
