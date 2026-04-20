<?php declare(strict_types=1); ?>

<?php
$fieldLabelClasses = 'text-sm font-extrabold text-neutral-800';
$fieldInputClasses = 'h-12 w-full rounded-2xl border border-neutral-300 bg-white px-4 text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:ring-2 focus:ring-brandSoft focus:border-brand-500';
$primaryButtonClasses = 'inline-flex w-full items-center justify-center rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:bg-brand-600';
$inlineLinkClasses = 'inline-flex items-center gap-1 font-extrabold text-brand-600 transition hover:text-brand-700';
?>

<h2 class="text-3xl font-heading font-extrabold text-neutral-950">Welcome Back</h2>
<p class="mt-2 text-sm text-neutral-600">Sign in to manage bookings, publish services, and track your activity.</p>

<form action="/login" method="post" class="mt-6 grid gap-4">
	<?php echo csrf_field(); ?>

	<div class="grid gap-1.5">
		<label for="email" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Email</label>
		<input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="grid gap-1.5">
		<label for="password" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Password</label>
		<input id="password" name="password" type="password" required minlength="8" maxlength="128" autocomplete="current-password" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="pt-1">
		<button type="submit" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Login</button>
	</div>
</form>

<p class="mt-3 text-sm text-neutral-600">
	No account yet?
	<a href="/register" class="<?php echo htmlspecialchars($inlineLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Create one <span aria-hidden="true">-></span></a>
</p>

<p class="mt-1.5 text-sm text-neutral-600">
	Forgot your password?
	<a href="/forgot-password" class="<?php echo htmlspecialchars($inlineLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Reset it <span aria-hidden="true">-></span></a>
</p>

<div class="mt-3 rounded-2xl border border-neutral-300 bg-surface-page p-4">
	<p class="text-xs font-extrabold uppercase tracking-[0.14em] text-brand-700">Demo Accounts</p>
	<p class="mt-2 text-sm text-neutral-700">Both: provider@example.com / [removed-demo-password]</p>
	<p class="text-sm text-neutral-700">Seeker: seeker@example.com / [removed-demo-password]</p>
</div>
