<?php declare(strict_types=1); ?>

<?php
$fieldLabelClasses = 'text-sm font-extrabold text-neutral-800';
$fieldInputClasses = 'h-12 w-full rounded-2xl border border-neutral-300 bg-white px-4 text-sm text-neutral-900 placeholder:text-neutral-500 focus:outline-none focus:ring-2 focus:ring-brandSoft focus:border-brand-500';
$primaryButtonClasses = 'inline-flex w-full items-center justify-center rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:bg-brand-600';
$inlineLinkClasses = 'inline-flex items-center gap-1 font-extrabold text-brand-600 transition hover:text-brand-700';

$emailError = (string) form_errors('email');
$passwordError = (string) form_errors('password');
$authError = (string) form_errors('auth');
$emailHasError = $emailError !== '';
$passwordHasError = $passwordError !== '' || $authError !== '';
$inputErrorClasses = ' border-danger-300 focus:border-danger-500 focus:ring-danger-200';
?>

<h2 class="text-3xl font-heading font-extrabold text-neutral-950">Welcome Back</h2>
<p class="mt-2 text-sm text-neutral-600">Sign in to manage bookings, publish services, and track your activity.</p>

<form action="/login" method="post" class="mt-6 grid gap-4" data-skip-client-validation novalidate>
	<?php echo csrf_field(); ?>

	<div class="grid gap-1.5" data-field>
		<label for="email" class="<?php echo e($fieldLabelClasses); ?>">Email</label>
		<input id="email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?php echo e(old_input('email')); ?>" class="<?php echo e($fieldInputClasses . ($emailHasError ? $inputErrorClasses : '')); ?>"<?php echo $emailHasError ? ' aria-invalid="true" data-invalid="true"' : ''; ?>>
		<?php if ($emailHasError): ?>
			<p class="text-xs font-semibold text-danger-700" data-field-error><?php echo e($emailError); ?></p>
		<?php endif; ?>
	</div>

	<div class="grid gap-1.5" data-field>
		<label for="password" class="<?php echo e($fieldLabelClasses); ?>">Password</label>
		<input id="password" name="password" type="password" required maxlength="128" autocomplete="current-password" class="<?php echo e($fieldInputClasses . ($passwordHasError ? $inputErrorClasses : '')); ?>"<?php echo $passwordHasError ? ' aria-invalid="true" data-invalid="true"' : ''; ?>>
		<?php if ($passwordHasError): ?>
			<p class="text-xs font-semibold text-danger-700" data-field-error><?php echo e($passwordError !== '' ? $passwordError : $authError); ?></p>
		<?php endif; ?>
	</div>

	<div class="pt-1">
		<button type="submit" class="<?php echo e($primaryButtonClasses); ?>">Login</button>
	</div>
</form>

<p class="mt-3 text-sm text-neutral-600">
	No account yet?
	<a href="/register" class="<?php echo e($inlineLinkClasses); ?>">Create one <span aria-hidden="true">-></span></a>
</p>

<p class="mt-1.5 text-sm text-neutral-600">
	Forgot your password?
	<a href="/forgot-password" class="<?php echo e($inlineLinkClasses); ?>">Reset it <span aria-hidden="true">-></span></a>
</p>

<div class="mt-3 rounded-2xl border border-neutral-300 bg-surface-page p-4">
	<p class="text-xs font-extrabold uppercase tracking-[0.14em] text-brand-700">Demo Accounts</p>
</div>
