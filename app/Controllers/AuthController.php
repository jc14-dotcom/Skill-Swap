<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class AuthController extends Controller
{
	private AuthService $auth;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->auth = new AuthService();
	}

	public function showLogin(): void
	{
		$this->view('auth.login', ['title' => 'Login'], 'layouts.guest');
	}

	public function showForgotPassword(): void
	{
		$this->view('auth.forgot-password', ['title' => 'Forgot Password'], 'layouts.guest');
	}

	public function login(): void
	{
		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'email' => 'required|email|max:190',
			'password' => 'required|string|min:8|max:128',
		]);

		if (!$valid) {
			$this->redirect('/login');
			return;
		}

		try {
			$this->auth->login((string) $clean['email'], (string) $clean['password']);
			Session::flash('success', 'Welcome back.');
			$this->redirect('/home');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['auth' => [$exception->getMessage()]]);
			Session::flash('old', ['email' => (string) $clean['email']]);
			$this->redirect('/login');
		}
	}

	public function showRegister(): void
	{
		$this->view('auth.register', ['title' => 'Register'], 'layouts.guest');
	}

	public function sendResetLink(): void
	{
		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'email' => 'required|email|max:190',
		]);

		if (!$valid) {
			$this->redirect('/forgot-password');
			return;
		}

		$token = $this->auth->requestPasswordReset((string) $clean['email']);

		if (is_string($token) && $token !== '') {
			Session::flash('reset_preview_url', '/reset-password?token=' . urlencode($token));
		}

		Session::flash('success', 'If an account exists for that email, a reset link is ready.');
		$this->redirect('/forgot-password');
	}

	public function showResetPassword(): void
	{
		$token = trim((string) $this->request->query('token', ''));
		if ($token === '' || !$this->auth->hasValidPasswordResetToken($token)) {
			Session::flash('errors', ['auth' => ['Reset token is invalid or expired.']]);
			$this->redirect('/forgot-password');
			return;
		}

		$this->view('auth.reset-password', [
			'title' => 'Reset Password',
			'token_hash' => hash('sha256', $token),
		], 'layouts.guest');
	}

	public function resetPassword(): void
	{
		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'token' => 'required|string|min:20|max:255',
			'password' => 'required|string|min:8|max:128|confirmed',
		]);

		$token = trim((string) ($clean['token'] ?? (string) $this->request->input('token', '')));

		if (!$valid) {
			$this->redirect('/reset-password?token=' . urlencode($token));
			return;
		}

		try {
			$this->auth->resetPassword($token, (string) $clean['password']);
			Session::flash('success', 'Password reset successful. You may now login.');
			$this->redirect('/login');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['auth' => [$exception->getMessage()]]);
			$this->redirect('/reset-password?token=' . urlencode($token));
		}
	}

	public function register(): void
	{
		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'name' => 'required|string|min:2|max:120',
			'email' => 'required|email|max:190',
			'password' => 'required|string|min:8|max:128|confirmed',
			'role' => 'required|in:seeker,provider,both',
		]);

		if (!$valid) {
			$this->redirect('/register');
			return;
		}

		try {
			$this->auth->register($clean);
			Session::flash('success', 'Account created successfully.');
			$this->redirect('/home');
		} catch (\Throwable $exception) {
			Session::flash('errors', ['auth' => [$exception->getMessage()]]);
			Session::flash('old', [
				'name' => (string) $clean['name'],
				'email' => (string) $clean['email'],
				'role' => (string) $clean['role'],
			]);
			$this->redirect('/register');
		}
	}

	public function logout(): void
	{
		$this->auth->logout();
		$this->redirect('/login');
	}
}
