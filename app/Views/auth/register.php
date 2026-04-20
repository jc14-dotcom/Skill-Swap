<?php declare(strict_types=1); ?>

<?php
$fieldLabelClasses = 'text-sm font-extrabold text-neutral-800';
$fieldInputClasses = 'h-12 w-full rounded-2xl border border-neutral-300 bg-white px-4 text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:ring-2 focus:ring-brandSoft focus:border-brand-500';
$primaryButtonClasses = 'inline-flex w-full items-center justify-center rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:bg-brand-600';
$inlineLinkClasses = 'inline-flex items-center gap-1 font-extrabold text-brand-600 transition hover:text-brand-700';
?>

<h2 class="text-3xl font-heading font-extrabold text-neutral-950">Create Account</h2>
<p class="mt-2 text-sm text-neutral-600">Start as seeker, provider, or both and unlock SkillSwap collaboration tools.</p>

<form action="/register" method="post" class="mt-6 grid gap-4">
	<?php echo csrf_field(); ?>

	<div class="grid gap-1.5">
		<label for="name" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Name</label>
		<input id="name" name="name" type="text" required minlength="2" maxlength="120" autocomplete="name" value="<?php echo htmlspecialchars((string) old_input('name'), ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="grid gap-1.5">
		<label for="email" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Email</label>
		<input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="grid gap-1.5">
		<label for="role" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Role</label>
		<?php $oldRole = (string) old_input('role', 'seeker'); ?>
		<select id="role" name="role" required class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?> pr-10">
			<option value="seeker" <?php echo $oldRole === 'seeker' ? 'selected' : ''; ?>>Seeker</option>
			<option value="provider" <?php echo $oldRole === 'provider' ? 'selected' : ''; ?>>Provider</option>
			<option value="both" <?php echo $oldRole === 'both' ? 'selected' : ''; ?>>Both (Seeker + Provider)</option>
		</select>
	</div>

	<div class="grid gap-1.5">
		<label for="password" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Password</label>
		<input id="password" name="password" type="password" required minlength="8" maxlength="128" autocomplete="new-password" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="grid gap-1.5">
		<label for="password_confirmation" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Confirm Password</label>
		<input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" maxlength="128" autocomplete="new-password" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="pt-1">
		<button type="submit" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Register</button>
	</div>
</form>

<p class="mt-3 text-sm text-neutral-600">
	Already have an account?
	<a href="/login" class="<?php echo htmlspecialchars($inlineLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Login <span aria-hidden="true">-></span></a>
</p>
