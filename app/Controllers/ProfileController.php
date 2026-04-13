<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;

class ProfileController extends Controller
{
	private User $users;

	public function __construct(Request $request, Response $response)
	{
		parent::__construct($request, $response);
		$this->users = new User();
	}

	public function settings(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		$user = $this->users->find((int) ($actor['id'] ?? 0));
		if (!is_array($user)) {
			Session::flash('errors', ['profile' => ['User account could not be loaded.']]);
			$this->redirect('/home');
			return;
		}

		unset($user['password']);

		$this->view('profile.settings', [
			'title' => 'Profile Settings',
			'profile' => $user,
		]);
	}

	public function updateSettings(): void
	{
		$actor = $this->user();
		if (!is_array($actor)) {
			$this->redirect('/login');
			return;
		}

		[$valid, $errors, $clean] = $this->validate($this->request->all(), [
			'name' => 'required|string|min:2|max:120',
			'email' => 'required|email|max:190',
			'password' => 'nullable|string|min:8|confirmed',
		]);

		if (!$valid) {
			$this->redirect('/profile/settings');
			return;
		}

		$userId = (int) ($actor['id'] ?? 0);
		if ($userId <= 0) {
			$this->redirect('/login');
			return;
		}

		$email = strtolower((string) ($clean['email'] ?? ''));
		if ($this->users->emailTakenByAnother($email, $userId)) {
			Session::flash('errors', ['profile' => ['Email is already in use by another account.']]);
			Session::flash('old', [
				'name' => (string) ($clean['name'] ?? ''),
				'email' => $email,
			]);
			$this->redirect('/profile/settings');
			return;
		}

		$payload = [
			'name' => (string) ($clean['name'] ?? ''),
			'email' => $email,
		];

		$password = (string) ($clean['password'] ?? '');
		if ($password !== '') {
			$payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
		}

		$this->users->updateProfile($userId, $payload);

		$updated = $this->users->find($userId);
		if (is_array($updated)) {
			unset($updated['password']);
			Session::put('auth.user', $updated);
		}

		Session::flash('success', 'Profile settings updated.');
		$this->redirect('/profile/settings');
	}
}
