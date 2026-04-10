<?php declare(strict_types=1); ?>

<div class="mb-6 flex items-center justify-between">
	<h1 class="text-2xl font-semibold">Services</h1>
	<?php if (auth_check() && in_array(auth_role(), ['provider', 'admin'], true)): ?>
		<a href="/services/create" class="rounded bg-slate-900 px-4 py-2 text-white">Create Service</a>
	<?php endif; ?>
</div>

<?php if (($services ?? []) === []): ?>
	<p class="text-slate-600">No services available yet.</p>
<?php else: ?>
	<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
		<?php foreach ($services as $service): ?>
			<article class="rounded border border-slate-200 bg-white p-4">
				<h2 class="text-lg font-semibold text-slate-900">
					<?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?>
				</h2>
				<p class="mt-1 text-sm text-slate-600">
					Provider: <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?>
				</p>
				<p class="mt-2 text-sm text-slate-700 line-clamp-3">
					<?php echo htmlspecialchars((string) $service['description'], ENT_QUOTES, 'UTF-8'); ?>
				</p>
				<p class="mt-3 font-medium"><?php echo format_currency((float) $service['price']); ?></p>
				<a href="/services/<?php echo (int) $service['id']; ?>" class="mt-3 inline-block text-sm font-medium text-slate-900 underline">View service</a>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
