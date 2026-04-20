<?php declare(strict_types=1); ?>

<?php $previewUrl = (string) session_get('reset_preview_url', ''); ?>

<?php
$fieldLabelClasses = 'text-sm font-extrabold text-neutral-800';
$fieldInputClasses = 'h-12 w-full rounded-2xl border border-neutral-300 bg-white px-4 text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:ring-2 focus:ring-brandSoft focus:border-brand-500';
$primaryButtonClasses = 'inline-flex w-full items-center justify-center rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:bg-brand-600';
$inlineLinkClasses = 'inline-flex items-center gap-1 font-extrabold text-brand-600 transition hover:text-brand-700';
?>

<h2 class="text-3xl font-heading font-extrabold text-neutral-950">Forgot Password</h2>
<p class="mt-2 text-sm text-neutral-600">Enter your account email and we will prepare a reset link.</p>

<form action="/forgot-password" method="post" class="mt-6 grid gap-4">
	<?php echo csrf_field(); ?>

	<div class="grid gap-1.5">
		<label for="email" class="<?php echo htmlspecialchars($fieldLabelClasses, ENT_QUOTES, 'UTF-8'); ?>">Email</label>
		<input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($fieldInputClasses, ENT_QUOTES, 'UTF-8'); ?>">
	</div>

	<div class="pt-1">
		<button type="submit" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Generate Reset Link</button>
	</div>
</form>

<?php if ($previewUrl !== ''): ?>
	<div class="mt-4 rounded-2xl border border-neutral-300 bg-surface-page p-4">
		<p class="text-xs font-extrabold uppercase tracking-[0.14em] text-brand-700">Debug Reset Link</p>
		<p class="mt-2 text-sm text-neutral-700">
			<a href="<?php echo htmlspecialchars($previewUrl, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($inlineLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Open reset page <span aria-hidden="true">-></span></a>
		</p>
		<p class="mt-2 text-sm text-neutral-700">In production, the reset link should be delivered via email.</p>
	</div>
<?php endif; ?>

<p class="mt-3 text-sm text-neutral-600">
	Remembered your password?
	<a href="/login" class="<?php echo htmlspecialchars($inlineLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Back to login <span aria-hidden="true">-></span></a>
</p>
