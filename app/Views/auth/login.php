<?php declare(strict_types=1); ?>

<h2 class="mb-4 text-xl font-semibold">Login</h2>

<form action="/login" method="post" class="space-y-4">
	<?php echo csrf_field(); ?>

	<div>
		<label for="email" class="mb-1 block text-sm font-medium">Email</label>
		<input id="email" name="email" type="email" required
			   value="<?php echo htmlspecialchars((string) old_input('email'), ENT_QUOTES, 'UTF-8'); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="password" class="mb-1 block text-sm font-medium">Password</label>
		<input id="password" name="password" type="password" required class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<button type="submit" class="w-full rounded bg-slate-900 px-4 py-2 text-white">Login</button>
</form>

<p class="mt-4 text-sm text-slate-600">
	No account yet?
	<a href="/register" class="font-medium text-slate-900">Create one</a>
</p>

<div class="mt-5 rounded border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600">
	<p class="font-semibold text-slate-700">Demo Accounts</p>
	<p class="mt-1">Provider: provider@example.com / [removed-demo-password]</p>
	<p>Seeker: seeker@example.com / [removed-demo-password]</p>
</div>
