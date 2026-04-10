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

	public function login(): void
	{
		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'email' => 'required|email',
			'password' => 'required|string|min:6',
		]);

		if (!$valid) {
			$this->redirect('/login');
			return;
		}

		try {
			$this->auth->login((string) $clean['email'], (string) $clean['password']);
			Session::flash('success', 'Welcome back.');
			$this->redirect('/services');
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

	public function register(): void
	{
		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'name' => 'required|string|min:2|max:120',
			'email' => 'required|email|max:190',
			'password' => 'required|string|min:8|confirmed',
			'role' => 'required|in:seeker,provider',
		]);

		if (!$valid) {
			$this->redirect('/register');
			return;
		}

		try {
			$this->auth->register($clean);
			Session::flash('success', 'Account created successfully.');
			$this->redirect('/services');
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
