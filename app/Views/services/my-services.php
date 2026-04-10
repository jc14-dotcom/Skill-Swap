<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">My Services</h1>

<?php if (($services ?? []) === []): ?>
	<p class="text-slate-600">You have not created any services yet.</p>
<?php else: ?>
	<div class="grid gap-4 md:grid-cols-2">
		<?php foreach ($services as $service): ?>
			<article class="rounded border border-slate-200 bg-white p-4">
				<h2 class="text-lg font-semibold"><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
				<p class="mt-2 text-sm text-slate-600"><?php echo htmlspecialchars((string) $service['description'], ENT_QUOTES, 'UTF-8'); ?></p>
				<p class="mt-3 font-medium"><?php echo format_currency((float) $service['price']); ?></p>
				<div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
					<a class="underline" href="/services/<?php echo (int) $service['id']; ?>">Open</a>
					<a class="underline" href="/services/<?php echo (int) $service['id']; ?>/edit">Edit</a>
					<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');">
						<?php echo csrf_field(); ?>
						<input type="hidden" name="_method" value="DELETE">
						<button type="submit" class="text-rose-700 underline">Delete</button>
					</form>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
