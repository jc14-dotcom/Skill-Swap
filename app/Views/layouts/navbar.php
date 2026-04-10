<?php
declare(strict_types=1);

$user = auth_user();
?>
<header class="border-b border-slate-200 bg-white">
	<div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
		<a href="/services" class="text-lg font-bold text-slate-900">SkillSwap</a>

		<nav class="flex items-center gap-4 text-sm">
			<a href="/services" class="text-slate-700 hover:text-slate-900">Services</a>

			<?php if ($user): ?>
				<a href="/bookings/mine" class="text-slate-700 hover:text-slate-900">My Bookings</a>
				<a href="/bookings/provider" class="text-slate-700 hover:text-slate-900">Provider Bookings</a>

				<?php if (in_array((string) ($user['role'] ?? ''), ['provider', 'admin'], true)): ?>
					<a href="/services/create" class="text-slate-700 hover:text-slate-900">Create Service</a>
					<a href="/services/mine" class="text-slate-700 hover:text-slate-900">My Services</a>
				<?php endif; ?>

				<form action="/logout" method="post" class="inline-block">
					<?php echo csrf_field(); ?>
					<button type="submit" class="rounded bg-slate-900 px-3 py-1.5 text-white">Logout</button>
				</form>
			<?php else: ?>
				<a href="/login" class="text-slate-700 hover:text-slate-900">Login</a>
				<a href="/register" class="rounded bg-slate-900 px-3 py-1.5 text-white">Register</a>
			<?php endif; ?>
		</nav>
	</div>
</header>
