<?php declare(strict_types=1); ?>

<?php
$receipt = is_array($receipt ?? null) ? $receipt : [];
$breakdown = is_array($receipt['breakdown'] ?? null) ? $receipt['breakdown'] : [];
$price = (float) ($breakdown['price'] ?? 0);
$commission = (float) ($breakdown['commission'] ?? 0);
$providerNet = (float) ($breakdown['provider_net'] ?? 0);
$serviceTitle = (string) ($receipt['service_title'] ?? 'Service');
$providerName = (string) ($receipt['provider_name'] ?? 'Provider');
$paymentMethod = strtoupper((string) ($receipt['payment_method'] ?? 'DEMO'));
$paymentReference = (string) ($receipt['payment_reference'] ?? 'PAY-DEMO');
$paidAt = (string) ($receipt['paid_at'] ?? '');
?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(980px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-success-200 bg-success-50 p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-success-700">
				<span class="h-px w-8 bg-success-300"></span>
				Payment Successful
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-success-900 md:text-4xl">Thanks! Your payment is confirmed.</h1>
			<p class="mt-2 text-sm text-success-800">Booking for <?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?> has been marked as paid.</p>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),20rem]">
			<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<h2 class="text-2xl font-heading font-extrabold text-neutral-950">Payment Receipt</h2>
				<p class="mt-2 text-sm text-neutral-600">Reference: <?php echo htmlspecialchars($paymentReference, ENT_QUOTES, 'UTF-8'); ?></p>
				<p class="text-sm text-neutral-600">Method: <?php echo htmlspecialchars($paymentMethod, ENT_QUOTES, 'UTF-8'); ?></p>
				<?php if ($paidAt !== ''): ?>
					<p class="text-sm text-neutral-600">Paid at: <?php echo htmlspecialchars(format_datetime($paidAt, 'M d, Y h:i A'), ENT_QUOTES, 'UTF-8'); ?></p>
				<?php endif; ?>

				<div class="mt-6 rounded-3xl border border-neutral-200 bg-surface-page p-5">
					<div class="flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Service price</span>
						<span class="text-base font-extrabold text-neutral-900"><?php echo htmlspecialchars(format_currency($price), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="mt-2 flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Platform fee</span>
						<span class="text-sm font-extrabold text-amber-800"><?php echo htmlspecialchars(format_currency($commission), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="mt-2 flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Provider receives</span>
						<span class="text-sm font-extrabold text-success-700"><?php echo htmlspecialchars(format_currency($providerNet), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<hr class="my-4 border-neutral-200">
					<div class="flex items-center justify-between">
						<span class="text-base font-extrabold text-neutral-900">Total paid</span>
						<span class="text-lg font-extrabold text-neutral-950"><?php echo htmlspecialchars(format_currency($price), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
				</div>
			</section>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
					<span class="inline-flex rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-neutral-600">Provider</span>
					<h3 class="mt-3 text-xl font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars($providerName, ENT_QUOTES, 'UTF-8'); ?></h3>
					<p class="mt-2 text-sm text-neutral-600">The provider will receive their payout after marking the booking completed.</p>
					<a href="/bookings/mine" class="mt-4 inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Back to bookings <span aria-hidden="true">-></span></a>
				</section>
			</aside>
		</div>
	</div>
</section>
