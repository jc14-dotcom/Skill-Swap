<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		if (!defined('BASE_PATH')) {
			define('BASE_PATH', dirname(__DIR__, 2));
		}
	}

	protected function tearDown(): void
	{
		parent::tearDown();
	}

	protected function arraySubset(array $haystack, array $needle): void
	{
		foreach ($needle as $key => $value) {
			$this->assertArrayHasKey($key, $haystack, "Key '{$key}' not found in array");
			$this->assertEquals($value, $haystack[$key], "Value for key '{$key}' does not match");
		}
	}
}
