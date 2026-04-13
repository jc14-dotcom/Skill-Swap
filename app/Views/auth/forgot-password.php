<?php declare(strict_types=1); ?>

<?php $previewUrl = (string) session_get('reset_preview_url', ''); ?>

<h2>Forgot Password</h2>
<p class="ss-auth-note">Enter your account email and we will prepare a reset link.</p>

<form action="/forgot-password" method="post" class="ss-form-shell" style="max-width: none; box-shadow: none; border: 0; padding: 0; background: transparent;">
	<?php echo csrf_field(); ?>

	<div class="ss-field">
		<label for="email">Email</label>
		<input id="email" name="email" type="email" required value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
	</div>

	<div class="ss-actions">
		<button type="submit" class="ss-btn-soft" style="width: 100%;">Generate Reset Link</button>
	</div>
</form>

<?php if ($previewUrl !== ''): ?>
	<div class="ss-page-card" style="padding: 0.9rem; margin-top: 0.6rem;">
		<p class="ss-subtle">Debug Reset Link</p>
		<p style="margin-top: 0.4rem;">
			<a href="<?php echo htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8'); ?>" class="ss-mini-link">Open reset page <span class="ss-arrow">-></span></a>
		</p>
		<p style="margin-top: 0.45rem;">In production, the reset link should be delivered via email.</p>
	</div>
<?php endif; ?>

<p class="ss-auth-note" style="margin-top: 0.55rem;">
	Remembered your password?
	<a href="/login" class="ss-mini-link">Back to login <span class="ss-arrow">-></span></a>
</p>
