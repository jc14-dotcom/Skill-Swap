<?php declare(strict_types=1); ?>

<h2>Welcome Back</h2>
<p class="ss-auth-note">Sign in to manage bookings, publish services, and track your activity.</p>

<form action="/login" method="post" class="ss-form-shell" style="max-width: none; box-shadow: none; border: 0; padding: 0; background: transparent;">
	<?php echo csrf_field(); ?>

	<div class="ss-field">
		<label for="email">Email</label>
		<input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
	</div>

	<div class="ss-field">
		<label for="password">Password</label>
		<input id="password" name="password" type="password" required minlength="8" maxlength="128" autocomplete="current-password" class="ss-input">
	</div>

	<div class="ss-actions">
		<button type="submit" class="ss-btn-soft" style="width: 100%;">Login</button>
	</div>
</form>

<p class="ss-auth-note" style="margin-top: 0.55rem;">
	No account yet?
	<a href="/register" class="ss-mini-link">Create one <span class="ss-arrow">-></span></a>
</p>

<p class="ss-auth-note" style="margin-top: 0.3rem;">
	Forgot your password?
	<a href="/forgot-password" class="ss-mini-link">Reset it <span class="ss-arrow">-></span></a>
</p>

<div class="ss-page-card" style="padding: 0.9rem; margin-top: 0.55rem;">
	<p class="ss-subtle">Demo Accounts</p>
	<p class="mt-1">Provider: provider@example.com / [removed-demo-password]</p>
	<p>Seeker: seeker@example.com / [removed-demo-password]</p>
</div>
