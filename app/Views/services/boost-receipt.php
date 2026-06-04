<?php declare(strict_types=1); ?>

<?php
$service = is_array($service ?? null) ? $service : null;
$receipt = is_array($receipt ?? null) ? $receipt : [];
$serviceTitle = is_array($service) ? (string) ($service['title'] ?? 'Service') : 'Service';
$reference = (string) ($receipt['reference'] ?? 'BST-DEMO');
$amount = (float) ($receipt['amount'] ?? 0);
$days = $receipt['days'] ?? null;
$paymentMethod = strtoupper((string) ($receipt['payment_method'] ?? 'DEMO'));
$boostExpiresAt = (string) ($receipt['boost_expires_at'] ?? ($service['boost_expires_at'] ?? ''));
?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(980px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-amber-800">
				<span class="h-px w-8 bg-amber-300"></span>
				Boost Activated
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-amber-900 md:text-4xl">Your service is now boosted.</h1>
			<p class="mt-2 text-sm text-amber-800">Service: <?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?></p>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),20rem]">
			<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<h2 class="text-2xl font-heading font-extrabold text-neutral-950">Boost Receipt</h2>
				<p class="mt-2 text-sm text-neutral-600">Reference: <?php echo htmlspecialchars($reference, ENT_QUOTES, 'UTF-8'); ?></p>
				<p class="text-sm text-neutral-600">Method: <?php echo htmlspecialchars($paymentMethod, ENT_QUOTES, 'UTF-8'); ?></p>

				<div class="mt-6 rounded-3xl border border-neutral-200 bg-surface-page p-5">
					<div class="flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Boost plan</span>
						<span class="text-base font-extrabold text-neutral-900"><?php echo $days ? (int) $days . ' days' : 'Boosted'; ?></span>
					</div>
					<div class="mt-2 flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Amount paid</span>
						<span class="text-base font-extrabold text-brand-700"><?php echo htmlspecialchars(format_currency($amount), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<?php if ($boostExpiresAt !== ''): ?>
						<div class="mt-2 flex items-center justify-between">
							<span class="text-sm font-semibold text-neutral-600">Boost expires</span>
							<span class="text-sm font-extrabold text-neutral-900"><?php echo htmlspecialchars(format_datetime($boostExpiresAt, 'M d, Y h:i A'), ENT_QUOTES, 'UTF-8'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</section>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
					<span class="inline-flex rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-neutral-600">Next</span>
					<h3 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Your listing is highlighted.</h3>
					<p class="mt-2 text-sm text-neutral-600">You can review your boosted listing and track new bookings from seekers.</p>
					<a href="/services/mine" class="mt-4 inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Back to services <span aria-hidden="true">-></span></a>
				</section>
			</aside>
		</div>
	</div>
</section>
