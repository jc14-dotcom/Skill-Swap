<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<p class="text-slate-600">Service not found.</p>
<?php else: ?>
	<article class="rounded border border-slate-200 bg-white p-6">
		<h1 class="text-2xl font-semibold"><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
		<p class="mt-2 text-sm text-slate-600">
			Provider: <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?>
		</p>
		<p class="mt-4 whitespace-pre-line text-slate-700"><?php echo htmlspecialchars((string) $service['description'], ENT_QUOTES, 'UTF-8'); ?></p>
		<p class="mt-4 text-lg font-semibold"><?php echo format_currency((float) $service['price']); ?></p>
		<p class="mt-1 text-sm text-slate-500">Status: <?php echo htmlspecialchars((string) $service['status'], ENT_QUOTES, 'UTF-8'); ?></p>

		<?php if (($canManage ?? false) === true): ?>
			<div class="mt-4 flex flex-wrap gap-2">
				<a href="/services/<?php echo (int) $service['id']; ?>/edit" class="rounded border border-slate-300 px-3 py-2 text-sm">Edit Service</a>
				<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="_method" value="DELETE">
					<button type="submit" class="rounded border border-rose-300 bg-rose-50 px-3 py-2 text-sm text-rose-700">Delete Service</button>
				</form>
			</div>
		<?php endif; ?>

		<?php if (auth_check()): ?>
			<a href="/bookings/create?service_id=<?php echo (int) $service['id']; ?>" class="mt-5 inline-block rounded bg-slate-900 px-4 py-2 text-white">
				Book this service
			</a>
		<?php else: ?>
			<p class="mt-5 text-sm text-slate-600">Please <a href="/login" class="underline">login</a> to book this service.</p>
		<?php endif; ?>
	</article>
<?php endif; ?>
