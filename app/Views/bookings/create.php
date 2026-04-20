<?php declare(strict_types=1); ?>

<?php
$selectedService = is_array($service ?? null) ? $service : null;
$selectedServiceId = is_array($selectedService) ? (int) ($selectedService['id'] ?? 0) : 0;
$defaultAvatarImage = profile_image_url('');
?>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<?php if (is_array($selectedService)): ?>
			<?php $providerImage = profile_image_url((string) ($selectedService['provider_profile_picture'] ?? '')); ?>

			<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
				<form action="/bookings" method="post" class="space-y-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="service_id" value="<?php echo $selectedServiceId; ?>">

					<div>
						<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
							<span class="h-px w-8 bg-brand-300"></span>
							Confirm Booking
						</span>
						<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Book This Skill</h1>
						<p class="mt-3 text-sm leading-7 text-neutral-700">You selected a skill. Review the details then confirm your booking.</p>
					</div>

					<div class="rounded-3xl border border-neutral-200 bg-surface-page p-5">
						<h2 class="text-2xl font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars((string) ($selectedService['title'] ?? 'Skill Service'), ENT_QUOTES, 'UTF-8'); ?></h2>
						<div class="mt-3 flex items-center gap-3">
							<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($selectedService['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="h-10 w-10 rounded-full border border-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
							<p class="text-sm font-semibold text-neutral-700">By <?php echo htmlspecialchars((string) ($selectedService['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?></p>
						</div>

						<p class="mt-4 text-sm leading-7 text-neutral-700"><?php echo htmlspecialchars((string) ($selectedService['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="mt-4 text-2xl font-heading font-extrabold text-brand-700"><?php echo htmlspecialchars(format_currency((float) ($selectedService['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></p>
					</div>

					<div class="flex flex-wrap gap-3 pt-1">
						<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Confirm Booking <span aria-hidden="true">-></span></button>
						<a href="/services" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Back to skills</a>
						<a href="/services/<?php echo $selectedServiceId; ?>" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">View full details</a>
					</div>
				</form>

				<aside class="space-y-4">
					<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
						<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Next Step</span>
						<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Provider review starts after confirmation.</h2>
						<p class="mt-2 text-sm leading-7 text-neutral-700">Once submitted, your request appears in the provider queue for approval or rejection.</p>
						<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
							<li>Watch status updates in My Bookings.</li>
							<li>Use Messages after provider responds.</li>
							<li>Leave a review after completion.</li>
						</ul>
					</section>
				</aside>
			</div>
		<?php else: ?>
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">Select a skill first.</h3>
				<p class="mt-2 text-sm text-neutral-600">Open the skill board, pick a service, then continue booking.</p>
				<a href="/services" class="mt-5 inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Open skill board <span aria-hidden="true">-></span></a>
			</div>
		<?php endif; ?>
	</div>
</section>
