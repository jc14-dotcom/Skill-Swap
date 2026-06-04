<?php declare(strict_types=1); ?>

<?php
$booking = is_array($booking ?? null) ? $booking : null;
$breakdown = is_array($breakdown ?? null) ? $breakdown : [];
$price = (float) ($breakdown['price'] ?? 0);
$commission = (float) ($breakdown['commission'] ?? 0);
$providerNet = (float) ($breakdown['provider_net'] ?? 0);
$commissionRate = (float) ($breakdown['commission_rate'] ?? 0.10);
$serviceTitle = is_array($booking) ? (string) ($booking['service_title'] ?? 'Service') : 'Service';
$providerName = is_array($booking) ? (string) ($booking['provider_name'] ?? 'Provider') : 'Provider';
$bookingId = is_array($booking) ? (int) ($booking['id'] ?? 0) : 0;
?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1200px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Secure Checkout (Demo)
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Confirm Payment</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Payments are handled inside SkillSwap for transparency and automatic revenue split.</p>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
			<form action="/bookings/<?php echo (int) $bookingId; ?>/pay" method="post" class="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<?php echo csrf_field(); ?>

				<div>
					<h2 class="text-2xl font-heading font-extrabold text-neutral-950">Payment Details</h2>
					<p class="mt-2 text-sm text-neutral-600">Booking #<?php echo (int) $bookingId; ?> · <?php echo htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8'); ?></p>
					<p class="mt-1 text-sm text-neutral-600">Provider: <?php echo htmlspecialchars($providerName, ENT_QUOTES, 'UTF-8'); ?></p>
				</div>

				<div class="rounded-3xl border border-neutral-200 bg-surface-page p-5">
					<div class="flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Service price</span>
						<span class="text-base font-extrabold text-neutral-900"><?php echo htmlspecialchars(format_currency($price), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="mt-2 flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Platform fee (<?php echo (int) ($commissionRate * 100); ?>%)</span>
						<span class="text-sm font-extrabold text-amber-800"><?php echo htmlspecialchars(format_currency($commission), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<div class="mt-2 flex items-center justify-between">
						<span class="text-sm font-semibold text-neutral-600">Provider receives</span>
						<span class="text-sm font-extrabold text-success-700"><?php echo htmlspecialchars(format_currency($providerNet), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
					<hr class="my-4 border-neutral-200">
					<div class="flex items-center justify-between">
						<span class="text-base font-extrabold text-neutral-900">Total to pay</span>
						<span class="text-lg font-extrabold text-neutral-950"><?php echo htmlspecialchars(format_currency($price), ENT_QUOTES, 'UTF-8'); ?></span>
					</div>
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
					<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Pay now <span aria-hidden="true">-></span></button>
					<a href="/bookings/mine" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Back to bookings</a>
				</div>
			</form>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
					<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Why pay here?</span>
					<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">SkillSwap splits the revenue automatically.</h2>
					<p class="mt-2 text-sm leading-7 text-neutral-700">Your payment is processed inside the platform, so the provider receives 90% and SkillSwap keeps 10% as the platform fee.</p>
					<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
						<li>Transparent commission breakdown.</li>
						<li>Provider payout recorded after completion.</li>
						<li>Demo mode for presentation.</li>
					</ul>
				</section>
			</aside>
		</div>
	</div>
</section>
