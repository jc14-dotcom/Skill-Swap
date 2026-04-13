<?php declare(strict_types=1); ?>

<h2>Reset Password</h2>
<p class="ss-auth-note">Choose a new password with at least 8 characters.</p>

<form action="/reset-password" method="post" class="ss-form-shell" style="max-width: none; box-shadow: none; border: 0; padding: 0; background: transparent;">
	<?php echo csrf_field(); ?>
	<input type="hidden" name="token" value="<?php echo htmlspecialchars((string) ($token ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

	<div class="ss-field">
		<label for="password">New Password</label>
		<input id="password" name="password" type="password" required class="ss-input">
	</div>

	<div class="ss-field">
		<label for="password_confirmation">Confirm New Password</label>
		<input id="password_confirmation" name="password_confirmation" type="password" required class="ss-input">
	</div>

	<div class="ss-actions">
		<button type="submit" class="ss-btn-soft" style="width: 100%;">Update Password</button>
	</div>
</form>

<p class="ss-auth-note" style="margin-top: 0.55rem;">
	Need to return?
	<a href="/login" class="ss-mini-link">Back to login <span class="ss-arrow">-></span></a>
</p>
