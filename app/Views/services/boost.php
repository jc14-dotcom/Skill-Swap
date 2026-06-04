<?php declare(strict_types=1); ?>

<?php
$service = is_array($service ?? null) ? $service : null;
$boostPlans = is_array($boostPlans ?? null) ? $boostPlans : [];
$serviceId = is_array($service) ? (int) ($service['id'] ?? 0) : 0;
$serviceTitle = is_array($service) ? (string) ($service['title'] ?? 'Service') : 'Service';
?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1200px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Boost Listing (Demo)
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Promote Your Service</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Boosted listings appear higher in search results to attract more bookings.</p>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
			<form action="/services/<?php echo (int) $serviceId; ?>/boost" method="post" class="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<?php echo csrf_field(); ?>

				<div>
					<h2 class="text-2xl font-heading font-extrabold text-neutral-950">Boost Options</h2>
					<p class="mt-2 text-sm text-neutral-600">Service: <?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?></p>
				</div>

				<div class="grid gap-3">
					<?php foreach ($boostPlans as $plan): ?>
						<label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700">
							<div class="flex items-center gap-3">
								<input type="radio" name="plan" value="<?php echo (int) $plan['days']; ?>" class="h-4 w-4" <?php echo ((int) $plan['days'] === 7) ? 'checked' : ''; ?>>
								<span class="text-base font-extrabold text-neutral-900"><?php echo htmlspecialchars((string) $plan['label'], ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
							<span class="text-base font-extrabold text-brand-700"><?php echo htmlspecialchars(format_currency((float) $plan['price']), ENT_QUOTES, 'UTF-8'); ?></span>
						</label>
					<?php endforeach; ?>
				</div>

				<div>
					<p class="text-sm font-extrabold uppercase tracking-[0.16em] text-neutral-500">Payment Method</p>
					<div class="mt-3 grid gap-3">
						<label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700">
							<input type="radio" name="payment_method" value="gcash" class="h-4 w-4" checked>
							GCash (Demo)
						</label>
						<label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700">
							<input type="radio" name="payment_method" value="paymaya" class="h-4 w-4">
							PayMaya (Demo)
						</label>
						<label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-sm font-semibold text-neutral-700">
							<input type="radio" name="payment_method" value="paypal" class="h-4 w-4">
							PayPal (Demo)
						</label>
					</div>
				</div>

				<div class="flex flex-wrap gap-3">
					<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Pay &amp; Boost <span aria-hidden="true">-></span></button>
					<a href="/services/mine" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Back to services</a>
				</div>
			</form>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
					<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Boost Impact</span>
					<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Show up first in search.</h2>
					<p class="mt-2 text-sm leading-7 text-neutral-700">Boosts increase visibility during the selected period. This is a demo payment for presentation purposes.</p>
					<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
						<li>Highlights your service card.</li>
						<li>Ranks higher in marketplace results.</li>
						<li>Expires automatically after the plan ends.</li>
					</ul>
				</section>
			</aside>
		</div>
	</div>
</section>
