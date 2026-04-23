<?php declare(strict_types=1); ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Bookings
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Booking Overview</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Monitor both seeker and provider booking queues in one place.</p>
		</header>

		<div class="grid gap-6 lg:grid-cols-2">
			<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
				<h2 class="text-2xl font-heading font-extrabold text-neutral-950">As Seeker</h2>
				<?php if (($seekerBookings ?? []) === []): ?>
					<div class="mt-4 rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 px-5 py-6 text-sm text-neutral-600">
						No bookings yet.
					</div>
				<?php else: ?>
					<div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200">
						<table class="min-w-full divide-y divide-neutral-200 text-sm">
							<thead class="bg-surface-page text-left text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-600">
								<tr>
									<th class="px-4 py-3">Service</th>
									<th class="px-4 py-3">Provider</th>
									<th class="px-4 py-3">Status</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-neutral-200 bg-white">
								<?php foreach ($seekerBookings as $booking): ?>
									<tr>
										<td class="px-4 py-3 font-semibold text-neutral-900"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="px-4 py-3 text-neutral-700"><?php echo htmlspecialchars((string) $booking['provider_name'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="px-4 py-3">
											<span class="inline-flex items-center rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></span>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</section>

			<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
				<h2 class="text-2xl font-heading font-extrabold text-neutral-950">As Provider</h2>
				<?php if (($providerBookings ?? []) === []): ?>
					<div class="mt-4 rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 px-5 py-6 text-sm text-neutral-600">
						No incoming booking requests.
					</div>
				<?php else: ?>
					<div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200">
						<table class="min-w-full divide-y divide-neutral-200 text-sm">
							<thead class="bg-surface-page text-left text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-600">
								<tr>
									<th class="px-4 py-3">Service</th>
									<th class="px-4 py-3">Seeker</th>
									<th class="px-4 py-3">Status</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-neutral-200 bg-white">
								<?php foreach ($providerBookings as $booking): ?>
									<tr>
										<td class="px-4 py-3 font-semibold text-neutral-900"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="px-4 py-3 text-neutral-700"><?php echo htmlspecialchars((string) $booking['seeker_name'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td class="px-4 py-3">
											<span class="inline-flex items-center rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></span>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</section>
		</div>
	</div>
</section>
