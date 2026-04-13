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
			'password' => 'nullable|string|min:8|max:128|confirmed',
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

		$currentUser = $this->users->find($userId);
		if (!is_array($currentUser)) {
			Session::flash('errors', ['profile' => ['User account could not be loaded.']]);
			$this->redirect('/profile/settings');
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

		$removePictureRaw = strtolower(trim((string) $this->request->input('remove_profile_picture', '0')));
		$removeProfilePicture = in_array($removePictureRaw, ['1', 'true', 'yes', 'on'], true);

		$uploadError = null;
		$newPicturePath = null;
		$profilePicture = $_FILES['profile_picture'] ?? null;

		if (is_array($profilePicture)) {
			$errorCode = (int) ($profilePicture['error'] ?? UPLOAD_ERR_NO_FILE);
			if ($errorCode !== UPLOAD_ERR_NO_FILE) {
				if ($errorCode !== UPLOAD_ERR_OK) {
					$uploadError = 'Profile picture upload failed. Please try again.';
				} elseif ((int) ($profilePicture['size'] ?? 0) > 5 * 1024 * 1024) {
					$uploadError = 'Profile picture must be 5MB or smaller.';
				} else {
					$storedName = store_uploaded_file(
						'profile_picture',
						BASE_PATH . '/public/assets/images/profiles',
						['image/jpeg', 'image/png', 'image/webp', 'image/gif']
					);

					if (!is_string($storedName) || $storedName === '') {
						$uploadError = 'Invalid image file. Use JPG, PNG, WEBP, or GIF.';
					} else {
						$newPicturePath = '/assets/images/profiles/' . $storedName;
					}
				}
			}
		}

		if (is_string($uploadError)) {
			Session::flash('errors', ['profile' => [$uploadError]]);
			Session::flash('old', [
				'name' => (string) ($clean['name'] ?? ''),
				'email' => $email,
			]);
			$this->redirect('/profile/settings');
			return;
		}

		if (is_string($newPicturePath) && $newPicturePath !== '') {
			$payload['profile_picture'] = $newPicturePath;
		} elseif ($removeProfilePicture) {
			$payload['profile_picture'] = null;
		}

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

		$oldPath = (string) ($currentUser['profile_picture'] ?? '');
		$shouldDeleteOld = false;

		if (str_starts_with($oldPath, '/assets/images/profiles/')) {
			if (is_string($newPicturePath) && $newPicturePath !== '') {
				$shouldDeleteOld = $oldPath !== $newPicturePath;
			} elseif ($removeProfilePicture) {
				$shouldDeleteOld = true;
			}
		}

		if ($shouldDeleteOld) {
			$oldFile = basename($oldPath);
			$oldAbsolute = BASE_PATH . '/public/assets/images/profiles/' . $oldFile;
			if (is_file($oldAbsolute)) {
				@unlink($oldAbsolute);
			}
		}

		Session::flash('success', 'Profile settings updated.');
		$this->redirect('/profile/settings');
	}
}
