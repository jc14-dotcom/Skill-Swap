<?php declare(strict_types=1); ?>

<?php
$fieldLabelClasses = 'text-sm font-extrabold text-neutral-800';
$fieldInputClasses = 'h-12 w-full rounded-2xl border border-neutral-300 bg-white px-4 text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:ring-2 focus:ring-brandSoft focus:border-brand-500';
$primaryButtonClasses = 'inline-flex w-full items-center justify-center rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:bg-brand-600';
$inlineLinkClasses = 'inline-flex items-center gap-1 font-extrabold text-brand-600 transition hover:text-brand-700';
?>

<h2 class="text-3xl font-heading font-extrabold text-neutral-950">Reset Password</h2>
<p class="mt-2 text-sm text-neutral-600">Choose a new password with at least 8 characters.</p>

<form action="/reset-password" method="post" class="mt-6 grid gap-4">
	<?php echo csrf_field(); ?>
	<input type="hidden" name="token" value="<?php echo htmlspecialchars((string) ($token ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

	<div class="grid gap-1.5">
		<label for="password" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">New Password</label>
		<input id="password" name="password" type="password" required minlength="8" maxlength="128" autocomplete="new-password" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="grid gap-1.5">
		<label for="password_confirmation" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Confirm New Password</label>
		<input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" maxlength="128" autocomplete="new-password" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="pt-1">
		<button type="submit" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Update Password</button>
	</div>
</form>

<p class="mt-3 text-sm text-neutral-600">
	Need to return?
	<a href="/login" class="<?php echo htmlspecialchars($inlineLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Back to login <span aria-hidden="true">-></span></a>
</p>
