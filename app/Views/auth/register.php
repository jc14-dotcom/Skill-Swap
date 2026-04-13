<?php declare(strict_types=1); ?>

<h2>Create Account</h2>
<p class="ss-auth-note">Start as seeker or provider and unlock SkillSwap collaboration tools.</p>

<form action="/register" method="post" class="ss-form-shell" style="max-width: none; box-shadow: none; border: 0; padding: 0; background: transparent;">
	<?php echo csrf_field(); ?>

	<div class="ss-field">
		<label for="name">Name</label>
		<input id="name" name="name" type="text" required value="<?php echo htmlspecialchars((string) old_input('name'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
	</div>

	<div class="ss-field">
		<label for="email">Email</label>
		<input id="email" name="email" type="email" required value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
	</div>

	<div class="ss-field">
		<label for="role">Role</label>
		<?php $oldRole = (string) old_input('role', 'seeker'); ?>
		<select id="role" name="role" class="ss-select">
			<option value="seeker" <?php echo $oldRole === 'seeker' ? 'selected' : ''; ?>>Seeker</option>
			<option value="provider" <?php echo $oldRole === 'provider' ? 'selected' : ''; ?>>Provider</option>
		</select>
	</div>

	<div class="ss-field">
		<label for="password">Password</label>
		<input id="password" name="password" type="password" required class="ss-input">
	</div>

	<div class="ss-field">
		<label for="password_confirmation">Confirm Password</label>
		<input id="password_confirmation" name="password_confirmation" type="password" required class="ss-input">
	</div>

	<div class="ss-actions">
		<button type="submit" class="ss-btn-soft" style="width: 100%;">Register</button>
	</div>
</form>

<p class="ss-auth-note" style="margin-top: 0.45rem;">
	Already have an account?
	<a href="/login" class="ss-mini-link">Login <span class="ss-arrow">-></span></a>
</p>
