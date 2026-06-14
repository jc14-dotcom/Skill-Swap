<?php declare(strict_types=1); ?>

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
		<label for="email" class="<?php echo e($fieldLabelClasses); ?>">Email</label>
		<input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?php echo e(old_input('email')); ?>" class="<?php echo e($fieldInputClasses); ?>">
	</div>

	<div class="pt-1">
		<button type="submit" class="<?php echo e($primaryButtonClasses); ?>">Generate Reset Link</button>
	</div>
</form>

<p class="mt-3 text-sm text-neutral-600">
	Remembered your password?
	<a href="/login" class="<?php echo e($inlineLinkClasses); ?>">Back to login <span aria-hidden="true">-></span></a>
</p>
