<?php declare(strict_types=1); ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="flex flex-col gap-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8 lg:flex-row lg:items-end lg:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Provider Portal
				</span>
				<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">My Published Services</h1>
				<p class="mt-3 text-sm leading-7 text-neutral-700">Manage offers, edit details, and review booking readiness.</p>
			</div>
			<a href="/services/create" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Add New Service <span aria-hidden="true">-></span></a>
		</header>

		<?php if (($services ?? []) === []): ?>
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">You have not created any services yet.</h3>
				<p class="mt-2 text-sm text-neutral-600">Publish your first service and start receiving booking requests.</p>
			</div>
		<?php else: ?>
			<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
				<?php foreach ($services as $service): ?>
					<article class="group grid min-h-[16rem] gap-4 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft transition hover:-translate-y-0.5 hover:border-brand-200 hover:bg-surface-rose hover:shadow-brand" data-card-link="/services/<?php echo (int) $service['id']; ?>" role="link" tabindex="0" aria-label="Open <?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?> details">
						<span class="inline-flex w-fit items-center rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700"><?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></span>
						<h3 class="text-xl font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="text-sm leading-7 text-neutral-700"><?php echo htmlspecialchars((string) $service['description'], ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="text-2xl font-heading font-extrabold text-brand-700"><?php echo htmlspecialchars(format_currency((float) $service['price']), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="mt-auto flex flex-wrap gap-2">
							<a class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold text-neutral-900 transition hover:border-brand-500 hover:text-brand-700" href="/services/<?php echo (int) $service['id']; ?>" data-no-card-nav>Open</a>
							<a class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold text-neutral-900 transition hover:border-brand-500 hover:text-brand-700" href="/services/<?php echo (int) $service['id']; ?>/edit" data-no-card-nav>Edit</a>
							<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');" data-no-card-nav>
								<?php echo csrf_field(); ?>
								<input type="hidden" name="_method" value="DELETE">
								<button type="submit" class="inline-flex items-center rounded-full border border-danger-300 bg-white px-4 py-2 text-xs font-extrabold text-danger-700 transition hover:bg-danger-50">Delete</button>
							</form>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
