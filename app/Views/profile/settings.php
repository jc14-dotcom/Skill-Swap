<?php declare(strict_types=1); ?>

<?php $profileData = is_array($profile ?? null) ? $profile : []; ?>
<?php $profileImageUrl = profile_image_url((string) ($profileData['profile_picture'] ?? '')); ?>
<?php $defaultProfileImageUrl = profile_image_url(''); ?>
<?php $hasCustomProfileImage = $profileImageUrl !== $defaultProfileImageUrl; ?>

<section class="ss-section ss-profile-settings-section">
	<div class="ss-shell ss-profile-settings-shell">
		<div class="ss-profile-settings-head">
			<div>
				<span class="ss-section-label">Account</span>
				<h1 class="ss-page-title">Profile Settings</h1>
				<p class="ss-muted-block" style="margin-top: 0.45rem;">Manage your profile details used across SkillSwap.</p>
			</div>

			<div class="ss-profile-head-badges" aria-hidden="true">
				<span>Secure Profile</span>
				<span>Live Session</span>
			</div>
		</div>

		<div class="ss-profile-settings-grid">
			<form id="profile-settings-form" action="/profile/settings" method="post" enctype="multipart/form-data" class="ss-form-shell ss-profile-settings-form" data-submit-once data-submit-requires-dirty="true">
				<?php echo csrf_field(); ?>

				<div class="ss-profile-photo-shell ss-profile-field-full" data-profile-photo-shell data-has-custom-photo="<?php echo $hasCustomProfileImage ? '1' : '0'; ?>">
					<div class="ss-profile-photo-pane">
						<button type="button" class="ss-profile-photo-trigger" data-profile-photo-trigger aria-label="Choose a profile picture">
							<img
								src="<?php echo htmlspecialchars($profileImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
								alt="Profile picture"
								class="ss-profile-photo-preview"
								data-profile-photo-preview
								data-default-src="<?php echo htmlspecialchars($defaultProfileImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
								onerror="this.onerror=null;this.src='<?php echo htmlspecialchars($defaultProfileImageUrl, ENT_QUOTES, 'UTF-8'); ?>';"
							>
							<span class="ss-profile-photo-overlay" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M8.25 6.75L9.3 5.25H14.7L15.75 6.75H18.75C19.58 6.75 20.25 7.42 20.25 8.25V17.25C20.25 18.08 19.58 18.75 18.75 18.75H5.25C4.42 18.75 3.75 18.08 3.75 17.25V8.25C3.75 7.42 4.42 6.75 5.25 6.75H8.25Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
									<circle cx="12" cy="12.75" r="3" stroke="currentColor" stroke-width="1.6"/>
								</svg>
								<span>Change</span>
							</span>
						</button>

						<button type="button" class="ss-icon-action-btn ss-icon-action-danger ss-photo-remove-btn" data-profile-photo-remove>
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M4.5 7.5H19.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
								<path d="M9.75 10.5V16.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
								<path d="M14.25 10.5V16.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
								<path d="M7.5 7.5V6C7.5 5.17 8.17 4.5 9 4.5H15C15.83 4.5 16.5 5.17 16.5 6V7.5" stroke="currentColor" stroke-width="1.6"/>
								<path d="M6.75 7.5V17.25C6.75 18.08 7.42 18.75 8.25 18.75H15.75C16.58 18.75 17.25 18.08 17.25 17.25V7.5" stroke="currentColor" stroke-width="1.6"/>
							</svg>
							<span>Remove</span>
						</button>
					</div>

					<div class="ss-profile-inline-fields">
						<div class="ss-profile-identity-grid">
							<div class="ss-field">
								<label for="name" class="ss-label-with-icon">
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<circle cx="12" cy="8.25" r="3.25" stroke="currentColor" stroke-width="1.6"/>
										<path d="M5.25 18C5.25 15.38 8.27 13.5 12 13.5C15.73 13.5 18.75 15.38 18.75 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
									</svg>
									<span>Display Name</span>
								</label>
								<input
									id="name"
									name="name"
									type="text"
									required
									maxlength="120"
									class="ss-input"
									value="<?php echo htmlspecialchars((string) old_input('name', (string) ($profileData['name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"
								>
							</div>

							<div class="ss-field">
								<label for="email" class="ss-label-with-icon">
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<rect x="3.75" y="5.25" width="16.5" height="13.5" rx="1.8" stroke="currentColor" stroke-width="1.6"/>
										<path d="M4.5 6L12 11.25L19.5 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<span>Email Address</span>
								</label>
								<input
									id="email"
									name="email"
									type="email"
									required
									maxlength="190"
									class="ss-input"
									value="<?php echo htmlspecialchars((string) old_input('email', (string) ($profileData['email'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"
								>
							</div>
						</div>

						<p class="ss-subtle ss-photo-helper">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M8.25 6.75L9.3 5.25H14.7L15.75 6.75H18.75C19.58 6.75 20.25 7.42 20.25 8.25V17.25C20.25 18.08 19.58 18.75 18.75 18.75H5.25C4.42 18.75 3.75 18.08 3.75 17.25V8.25C3.75 7.42 4.42 6.75 5.25 6.75H8.25Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
								<circle cx="12" cy="12.75" r="3" stroke="currentColor" stroke-width="1.6"/>
							</svg>
							<span>Click the photo to upload a new one, or remove it to use the default avatar.</span>
						</p>
					</div>

					<input id="profile_picture" name="profile_picture" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="ss-input ss-file-input ss-file-input-hidden" data-profile-picture-input>
					<input type="hidden" name="remove_profile_picture" value="0" data-remove-profile-picture>
				</div>

				<div class="ss-profile-field-grid">
					<div class="ss-field ss-profile-field-full">
						<label for="role" class="ss-label-with-icon">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M12 3.75L14.06 7.92L18.66 8.59L15.33 11.84L16.12 16.41L12 14.25L7.88 16.41L8.67 11.84L5.34 8.59L9.94 7.92L12 3.75Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
							</svg>
							<span>Role</span>
						</label>
						<input id="role" type="text" class="ss-input" value="<?php echo htmlspecialchars(strtoupper((string) ($profileData['role'] ?? 'member')), ENT_QUOTES, 'UTF-8'); ?>" disabled>
					</div>

					<div class="ss-field">
						<label for="password" class="ss-label-with-icon">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<rect x="4.5" y="10.5" width="15" height="9" rx="2" stroke="currentColor" stroke-width="1.6"/>
								<path d="M8.25 10.5V8.25C8.25 6.18 9.93 4.5 12 4.5C14.07 4.5 15.75 6.18 15.75 8.25V10.5" stroke="currentColor" stroke-width="1.6"/>
							</svg>
							<span>New Password (optional)</span>
						</label>
						<input id="password" name="password" type="password" minlength="8" maxlength="128" class="ss-input" placeholder="Leave blank to keep current password">
					</div>

					<div class="ss-field">
						<label for="password_confirmation" class="ss-label-with-icon">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
								<path d="M7.5 12.75L10.5 15.75L16.5 9.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
								<rect x="4.5" y="4.5" width="15" height="15" rx="2" stroke="currentColor" stroke-width="1.6"/>
							</svg>
							<span>Confirm New Password</span>
						</label>
						<input id="password_confirmation" name="password_confirmation" type="password" minlength="8" maxlength="128" class="ss-input">
					</div>
				</div>

				<div class="ss-actions">
					<button type="submit" class="ss-btn-soft" data-submit-once-button data-loading-text="Saving..." disabled>
						<span class="ss-btn-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M5.25 4.5H16.5L18.75 6.75V19.5H5.25V4.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
								<path d="M8.25 4.5V10.5H15V4.5" stroke="currentColor" stroke-width="1.6"/>
								<path d="M8.25 19.5V14.25H15.75V19.5" stroke="currentColor" stroke-width="1.6"/>
							</svg>
						</span>
						<span>Save Changes</span>
					</button>
					<a href="/home" class="ss-btn-outline">
						<span class="ss-btn-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9.75 6.75L4.5 12L9.75 17.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M4.5 12H19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
							</svg>
						</span>
						<span>Back to Home</span>
					</a>
				</div>
			</form>

			<aside class="ss-profile-settings-aside">
				<div class="ss-profile-accent-panel">
					<span class="ss-profile-accent-tag">PROFILE ACCENT</span>
					<h2>Keep your account details current.</h2>
					<p>Accurate profile details make collaboration smoother and help members identify the right booking counterpart.</p>
					<ul class="ss-profile-accent-list">
						<li>Use a clear display name for better trust</li>
						<li>Keep your email updated for account recovery</li>
						<li>Rotate your password regularly for security</li>
					</ul>
				</div>
			</aside>
		</div>
	</div>
</section>
