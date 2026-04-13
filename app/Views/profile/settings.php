<?php declare(strict_types=1); ?>

<?php $profileData = is_array($profile ?? null) ? $profile : []; ?>

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
			<form action="/profile/settings" method="post" class="ss-form-shell ss-profile-settings-form">
				<?php echo csrf_field(); ?>

				<div class="ss-profile-field-grid">
					<div class="ss-field">
						<label for="name">Display Name</label>
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
						<label for="email">Email Address</label>
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

					<div class="ss-field ss-profile-field-full">
						<label for="role">Role</label>
						<input id="role" type="text" class="ss-input" value="<?php echo htmlspecialchars(strtoupper((string) ($profileData['role'] ?? 'member')), ENT_QUOTES, 'UTF-8'); ?>" disabled>
					</div>

					<div class="ss-field">
						<label for="password">New Password (optional)</label>
						<input id="password" name="password" type="password" minlength="8" class="ss-input" placeholder="Leave blank to keep current password">
					</div>

					<div class="ss-field">
						<label for="password_confirmation">Confirm New Password</label>
						<input id="password_confirmation" name="password_confirmation" type="password" minlength="8" class="ss-input">
					</div>
				</div>

				<div class="ss-actions">
					<button type="submit" class="ss-btn-soft">Save Changes <span class="ss-arrow">-></span></button>
					<a href="/home" class="ss-btn-outline">Back to Home</a>
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

				<div class="ss-page-card ss-profile-mini-card">
					<p class="ss-subtle">Current Snapshot</p>
					<div class="ss-profile-mini-row">
						<span>Name</span>
						<strong><?php echo htmlspecialchars((string) ($profileData['name'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
					<div class="ss-profile-mini-row">
						<span>Email</span>
						<strong><?php echo htmlspecialchars((string) ($profileData['email'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
					<div class="ss-profile-mini-row">
						<span>Member Since</span>
						<strong><?php echo htmlspecialchars(format_datetime((string) ($profileData['created_at'] ?? ''), 'M d, Y'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
				</div>
			</aside>
		</div>
	</div>
</section>
