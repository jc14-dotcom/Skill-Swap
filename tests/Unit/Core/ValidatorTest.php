<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Core\Validator;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
	public function testRequiredRuleFailsOnEmptyValue(): void
	{
		[$valid, $errors] = Validator::validate(['name' => ''], ['name' => 'required']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('name', $errors);
	}

	public function testRequiredRulePassesOnFilledValue(): void
	{
		[$valid] = Validator::validate(['name' => 'John'], ['name' => 'required']);

		$this->assertTrue($valid);
	}

	public function testEmailRuleRejectsInvalidEmail(): void
	{
		[$valid, $errors] = Validator::validate(['email' => 'not-an-email'], ['email' => 'required|email']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('email', $errors);
	}

	public function testEmailRuleAcceptsValidEmail(): void
	{
		[$valid] = Validator::validate(['email' => 'user@example.com'], ['email' => 'required|email']);

		$this->assertTrue($valid);
	}

	public function testMinRuleFailsOnShortString(): void
	{
		[$valid, $errors] = Validator::validate(['name' => 'Ab'], ['name' => 'required|min:3']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('name', $errors);
	}

	public function testMinRulePassesOnAdequateString(): void
	{
		[$valid] = Validator::validate(['name' => 'John'], ['name' => 'required|min:3']);

		$this->assertTrue($valid);
	}

	public function testMaxRuleFailsOnLongString(): void
	{
		[$valid, $errors] = Validator::validate(['name' => 'Very long name'], ['name' => 'required|max:5']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('name', $errors);
	}

	public function testInRuleRejectsInvalidValue(): void
	{
		[$valid, $errors] = Validator::validate(['status' => 'invalid'], ['status' => 'required|in:active,inactive']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('status', $errors);
	}

	public function testInRuleAcceptsValidValue(): void
	{
		[$valid] = Validator::validate(['status' => 'active'], ['status' => 'required|in:active,inactive']);

		$this->assertTrue($valid);
	}

	public function testConfirmedRuleFailsOnMismatch(): void
	{
		[$valid, $errors] = Validator::validate(
			['password' => 'secret', 'password_confirmation' => 'different'],
			['password' => 'required|confirmed']
		);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('password', $errors);
	}

	public function testConfirmedRulePassesOnMatch(): void
	{
		[$valid] = Validator::validate(
			['password' => 'secret', 'password_confirmation' => 'secret'],
			['password' => 'required|confirmed']
		);

		$this->assertTrue($valid);
	}

	public function testNullableRuleAllowsNull(): void
	{
		[$valid, $clean] = Validator::validate([], ['bio' => 'nullable|string']);

		$this->assertTrue($valid);
		$this->assertArrayNotHasKey('bio', $clean);
	}

	public function testNullableRuleAllowsEmptyString(): void
	{
		[$valid, $clean] = Validator::validate(['bio' => ''], ['bio' => 'nullable|string']);

		$this->assertTrue($valid);
		$this->assertNull($clean['bio'] ?? null);
	}

	public function testIntegerRuleRejectsNonInteger(): void
	{
		[$valid, $errors] = Validator::validate(['age' => 'abc'], ['age' => 'required|integer']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('age', $errors);
	}

	public function testIntegerRuleAcceptsInteger(): void
	{
		[$valid] = Validator::validate(['age' => '25'], ['age' => 'required|integer']);

		$this->assertTrue($valid);
	}

	public function testUrlRuleRejectsInvalidUrl(): void
	{
		[$valid, $errors] = Validator::validate(['website' => 'not-a-url'], ['website' => 'url']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('website', $errors);
	}

	public function testUrlRuleAcceptsValidUrl(): void
	{
		[$valid] = Validator::validate(['website' => 'https://example.com'], ['website' => 'url']);

		$this->assertTrue($valid);
	}

	public function testAlphaRuleRejectsNonAlpha(): void
	{
		[$valid, $errors] = Validator::validate(['name' => 'John123'], ['name' => 'alpha']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('name', $errors);
	}

	public function testAlphaRuleAcceptsAlphaOnly(): void
	{
		[$valid] = Validator::validate(['name' => 'John'], ['name' => 'alpha']);

		$this->assertTrue($valid);
	}

	public function testUuidRuleRejectsInvalidUuid(): void
	{
		[$valid, $errors] = Validator::validate(['id' => 'not-a-uuid'], ['id' => 'uuid']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('id', $errors);
	}

	public function testUuidRuleAcceptsValidUuid(): void
	{
		[$valid] = Validator::validate(['id' => '550e8400-e29b-41d4-a716-446655440000'], ['id' => 'uuid']);

		$this->assertTrue($valid);
	}

	public function testNotInRuleRejectsForbiddenValue(): void
	{
		[$valid, $errors] = Validator::validate(['status' => 'deleted'], ['status' => 'not_in:deleted,archived']);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('status', $errors);
	}

	public function testNotInRuleAcceptsAllowedValue(): void
	{
		[$valid] = Validator::validate(['status' => 'active'], ['status' => 'not_in:deleted,archived']);

		$this->assertTrue($valid);
	}

	public function testBooleanRuleAcceptsValidBooleans(): void
	{
		$this->assertTrue(Validator::validate(['active' => true], ['active' => 'boolean'])[0]);
		$this->assertTrue(Validator::validate(['active' => false], ['active' => 'boolean'])[0]);
		$this->assertTrue(Validator::validate(['active' => '1'], ['active' => 'boolean'])[0]);
		$this->assertTrue(Validator::validate(['active' => '0'], ['active' => 'boolean'])[0]);
	}

	public function testCustomRuleViaExtend(): void
	{
		Validator::extend('positive', fn(mixed $value): bool => is_numeric($value) && (float) $value > 0);

		[$valid] = Validator::validate(['amount' => '10'], ['amount' => 'required|positive']);
		$this->assertTrue($valid);

		[$valid] = Validator::validate(['amount' => '-5'], ['amount' => 'required|positive']);
		$this->assertFalse($valid);
	}

	public function testCleanDataIsReturned(): void
	{
		[$valid, $errors, $clean] = Validator::validate(
			['name' => '  John  ', 'email' => 'john@example.com'],
			['name' => 'required|string', 'email' => 'required|email']
		);

		$this->assertTrue($valid);
		$this->assertEquals('John', $clean['name']);
		$this->assertEquals('john@example.com', $clean['email']);
	}

	public function testMultipleFieldsValidation(): void
	{
		[$valid, $errors] = Validator::validate(
			['name' => '', 'email' => 'invalid'],
			['name' => 'required', 'email' => 'required|email']
		);

		$this->assertFalse($valid);
		$this->assertArrayHasKey('name', $errors);
		$this->assertArrayHasKey('email', $errors);
	}

	public function testSizeRuleForStrings(): void
	{
		[$valid] = Validator::validate(['code' => 'ABCD'], ['code' => 'size:4']);
		$this->assertTrue($valid);

		[$valid] = Validator::validate(['code' => 'ABC'], ['code' => 'size:4']);
		$this->assertFalse($valid);
	}
}
