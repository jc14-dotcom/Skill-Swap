<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<section class="py-14 md:py-16">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h2 class="text-2xl font-heading font-extrabold text-neutral-950">Service not found.</h2>
				<p class="mt-2 text-sm text-neutral-600">The requested service could not be loaded for editing.</p>
				<a href="/services/mine" class="mt-5 inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Back to my services <span aria-hidden="true">-></span></a>
			</div>
		</div>
	</section>
<?php else: ?>
	<?php $status = (string) old_input('status', (string) $service['status']); ?>
	<section class="py-14 md:py-16">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
			<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Provider Portal
				</span>
				<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Edit Service</h1>
				<p class="mt-3 text-sm leading-7 text-neutral-700">Update pricing, description, and status while keeping bookings consistent.</p>
			</header>

			<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
				<form action="/services/<?php echo (int) $service['id']; ?>" method="post" class="space-y-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="_method" value="PATCH">

					<div class="grid gap-2" data-field>
						<label for="title" class="text-sm font-extrabold text-neutral-800">Service title</label>
						<input id="title" name="title" type="text" required value="<?php echo htmlspecialchars((string) old_input('title', (string) $service['title']), ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
					</div>

					<div class="grid gap-2" data-field>
						<label for="description" class="text-sm font-extrabold text-neutral-800">Description</label>
						<textarea id="description" name="description" rows="6" required class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-7 text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><?php echo htmlspecialchars((string) old_input('description', (string) $service['description']), ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>

					<div class="grid gap-2" data-field>
						<label for="price" class="text-sm font-extrabold text-neutral-800">Price</label>
						<input id="price" name="price" type="number" min="1" step="0.01" required value="<?php echo htmlspecialchars((string) old_input('price', (string) $service['price']), ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
					</div>

					<div class="grid gap-2" data-field>
						<label for="status" class="text-sm font-extrabold text-neutral-800">Status</label>
						<select id="status" name="status" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
							<option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
							<option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
						</select>
					</div>

					<div class="flex flex-wrap gap-3 pt-1">
						<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Update Service <span aria-hidden="true">-></span></button>
						<a href="/services/<?php echo (int) $service['id']; ?>" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Cancel</a>
					</div>
				</form>

				<aside class="space-y-4">
					<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
						<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Edit Strategy</span>
						<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Adjust details without confusing active clients.</h2>
						<p class="mt-2 text-sm leading-7 text-neutral-700">Keep your listing aligned with what current and future bookings can realistically expect.</p>
						<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
							<li>Reflect any scope change in the description.</li>
							<li>Avoid frequent price changes in short intervals.</li>
							<li>Set inactive if you need time to update workflow.</li>
						</ul>
					</section>

					<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
						<h3 class="text-base font-heading font-extrabold text-neutral-950">Service Controls</h3>
						<div class="mt-3 space-y-2 text-sm">
							<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Current service ID</span><strong class="font-extrabold text-neutral-900">#<?php echo (int) $service['id']; ?></strong></div>
							<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Status toggles</span><strong class="font-extrabold text-neutral-900">Live now</strong></div>
						</div>
					</section>

					<section class="rounded-3xl border border-danger-200 bg-danger-50 p-6 shadow-soft">
						<h3 class="text-base font-heading font-extrabold text-danger-800">Danger Zone</h3>
						<p class="mt-2 text-sm text-danger-700">Deleting a service is permanent and may affect past references.</p>
						<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service? This action cannot be undone.');" class="mt-4">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="_method" value="DELETE">
							<button type="submit" class="inline-flex items-center rounded-full border border-danger-300 bg-white px-4 py-2 text-xs font-extrabold text-danger-700 transition hover:bg-danger-100">Delete Service</button>
						</form>
					</section>
				</aside>
			</div>
		</div>
	</section>
<?php endif; ?>
