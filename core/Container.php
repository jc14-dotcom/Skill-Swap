<?php

declare(strict_types=1);

namespace Core;

/**
 * Simple Dependency Injection Container.
 *
 * Supports binding, singleton, instance registration, and auto-wiring.
 * Auto-wiring resolves constructor parameters from registered bindings.
 *
 * @example
 * $container = new Container();
 * $container->singleton(Logger::class, fn() => new Logger());
 * $logger = $container->make(Logger::class);
 */
class Container
{
	/** @var array<string, callable(Container): mixed> Factory callables for new instances */
	private array $bindings = [];

	/** @var array<string, callable(Container): mixed> Factory callables for singletons */
	private array $singletons = [];

	/** @var array<string, mixed> Resolved singleton and explicit instances */
	private array $instances = [];

	/**
	 * Register a binding that creates a new instance each time.
	 *
	 * @param string $abstract Class or interface name
	 * @param callable(Container): mixed $concrete Factory that returns the instance
	 */
	public function bind(string $abstract, callable $concrete): void
	{
		$this->bindings[$abstract] = $concrete;
	}

	/**
	 * Register a binding that creates only one instance (lazy-loaded).
	 *
	 * @param string $abstract Class or interface name
	 * @param callable(Container): mixed $concrete Factory that returns the singleton
	 */
	public function singleton(string $abstract, callable $concrete): void
	{
		$this->singletons[$abstract] = $concrete;
	}

	/**
	 * Register an existing instance directly.
	 *
	 * @param string $abstract Class or interface name
	 * @param mixed $instance The instance to register
	 */
	public function instance(string $abstract, mixed $instance): void
	{
		$this->instances[$abstract] = $instance;
	}

	/**
	 * Resolve an instance from the container.
	 *
	 * Resolution order:
	 * 1. Explicit instances (instance())
	 * 2. Singletons (singleton()) - lazy-loaded and cached
	 * 3. Bindings (bind()) - new instance each time
	 * 4. Auto-wiring via reflection
	 *
	 * @param string $abstract Class or interface name
	 * @return mixed The resolved instance
	 * @throws \RuntimeException If no binding or auto-wire candidate exists
	 */
	public function make(string $abstract): mixed
	{
		if (isset($this->instances[$abstract])) {
			return $this->instances[$abstract];
		}

		if (isset($this->singletons[$abstract])) {
			$this->instances[$abstract] = ($this->singletons[$abstract])($this);
			return $this->instances[$abstract];
		}

		if (isset($this->bindings[$abstract])) {
			return ($this->bindings[$abstract])($this);
		}

		if (class_exists($abstract)) {
			return $this->build($abstract);
		}

		throw new \RuntimeException("No binding found for: {$abstract}");
	}

	/**
	 * Check if a binding or class exists in the container.
	 *
	 * @param string $abstract Class or interface name
	 * @return bool True if resolvable
	 */
	public function has(string $abstract): bool
	{
		return isset($this->bindings[$abstract])
			|| isset($this->singletons[$abstract])
			|| isset($this->instances[$abstract])
			|| class_exists($abstract);
	}

	/**
	 * Build an instance using reflection-based auto-wiring.
	 *
	 * @param string $class Fully qualified class name
	 * @return mixed The constructed instance
	 * @throws \RuntimeException If class is not instantiable or dependencies cannot be resolved
	 */
	private function build(string $class): mixed
	{
		$reflector = new \ReflectionClass($class);

		if (!$reflector->isInstantiable()) {
			throw new \RuntimeException("Cannot instantiate: {$class}");
		}

		$constructor = $reflector->getConstructor();

		if ($constructor === null) {
			return new $class();
		}

		$parameters = $constructor->getParameters();
		$dependencies = [];

		foreach ($parameters as $parameter) {
			$type = $parameter->getType();

			if ($type === null || $type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
				if ($parameter->isDefaultValueAvailable()) {
					$dependencies[] = $parameter->getDefaultValue();
				} elseif ($parameter->isOptional()) {
					$dependencies[] = null;
				} else {
					throw new \RuntimeException(
						"Cannot resolve parameter \${$parameter->getName()} of {$class}::__construct"
					);
				}
				continue;
			}

			$typeName = $type->getName();

			if ($this->has($typeName)) {
				$dependencies[] = $this->make($typeName);
			} elseif ($parameter->isDefaultValueAvailable()) {
				$dependencies[] = $parameter->getDefaultValue();
			} elseif ($type->allowsNull()) {
				$dependencies[] = null;
			} else {
				throw new \RuntimeException(
					"Cannot resolve type {$typeName} for parameter \${$parameter->getName()} of {$class}::__construct"
				);
			}
		}

		return $reflector->newInstanceArgs($dependencies);
	}
}
