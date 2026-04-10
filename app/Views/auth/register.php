<?php declare(strict_types=1); ?>

<h2 class="mb-4 text-xl font-semibold">Create Account</h2>

<form action="/register" method="post" class="space-y-4">
	<?php echo csrf_field(); ?>

	<div>
		<label for="name" class="mb-1 block text-sm font-medium">Name</label>
		<input id="name" name="name" type="text" required
			   value="<?php echo htmlspecialchars((string) old_input('name'), ENT_QUOTES, 'UTF-8'); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="email" class="mb-1 block text-sm font-medium">Email</label>
		<input id="email" name="email" type="email" required
			   value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="role" class="mb-1 block text-sm font-medium">Role</label>
		<?php $oldRole = (string) old_input('role', 'seeker'); ?>
		<select id="role" name="role" class="w-full rounded border border-slate-300 px-3 py-2">
			<option value="seeker" <?php echo $oldRole === 'seeker' ? 'selected' : ''; ?>>Seeker</option>
			<option value="provider" <?php echo $oldRole === 'provider' ? 'selected' : ''; ?>>Provider</option>
		</select>
	</div>

	<div>
		<label for="password" class="mb-1 block text-sm font-medium">Password</label>
		<input id="password" name="password" type="password" required class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm Password</label>
		<input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<button type="submit" class="w-full rounded bg-slate-900 px-4 py-2 text-white">Register</button>
</form>

<p class="mt-4 text-sm text-slate-600">
	Already have an account?
	<a href="/login" class="font-medium text-slate-900">Login</a>
</p>
