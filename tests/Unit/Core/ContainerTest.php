<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Core\Container;
use Tests\TestCase;

class ContainerTest extends TestCase
{
	private Container $container;

	protected function setUp(): void
	{
		parent::setUp();
		$this->container = new Container();
	}

	public function testMakeWithExplicitInstance(): void
	{
		$obj = new \stdClass();
		$this->container->instance(\stdClass::class, $obj);

		$resolved = $this->container->make(\stdClass::class);

		$this->assertSame($obj, $resolved);
	}

	public function testSingletonReturnsSameInstance(): void
	{
		$this->container->singleton(\stdClass::class, fn(): \stdClass => new \stdClass());

		$first = $this->container->make(\stdClass::class);
		$second = $this->container->make(\stdClass::class);

		$this->assertSame($first, $second);
	}

	public function testBindReturnsNewInstanceEachTime(): void
	{
		$this->container->bind(\stdClass::class, fn(): \stdClass => new \stdClass());

		$first = $this->container->make(\stdClass::class);
		$second = $this->container->make(\stdClass::class);

		$this->assertNotSame($first, $second);
	}

	public function testHasReturnsTrueForRegisteredBinding(): void
	{
		$this->container->bind('TestBinding', fn(): string => 'test');

		$this->assertTrue($this->container->has('TestBinding'));
	}

	public function testHasReturnsFalseForUnknownBinding(): void
	{
		$this->assertFalse($this->container->has('NonExistent'));
	}

	public function testMakeThrowsExceptionForUnknownBinding(): void
	{
		$this->expectException(\RuntimeException::class);

		$this->container->make('NonExistent');
	}

	public function testAutoWiringResolvesDependencies(): void
	{
		$this->container->singleton(LoggerStub::class, fn(): LoggerStub => new LoggerStub());

		$resolved = $this->container->make(ServiceWithDependency::class);

		$this->assertInstanceOf(ServiceWithDependency::class, $resolved);
		$this->assertInstanceOf(LoggerStub::class, $resolved->logger);
	}

	public function testAutoWiringWithDefaultParameter(): void
	{
		$resolved = $this->container->make(ServiceWithOptional::class);

		$this->assertInstanceOf(ServiceWithOptional::class, $resolved);
		// Container resolves LoggerStub because it's a valid class (auto-wiring)
		// Even though it has a default value, the container prefers to resolve
		$this->assertInstanceOf(LoggerStub::class, $resolved->logger);
	}
}

class LoggerStub
{
}

class ServiceWithDependency
{
	public function __construct(
		public readonly LoggerStub $logger
	) {
	}
}

class ServiceWithOptional
{
	public function __construct(
		public readonly ?LoggerStub $logger = null
	) {
	}
}
