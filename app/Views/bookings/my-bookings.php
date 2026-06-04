<?php declare(strict_types=1); ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="flex flex-col gap-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8 lg:flex-row lg:items-end lg:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Seeker Zone
				</span>
				<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">My Booking Requests</h1>
				<p class="mt-3 text-sm leading-7 text-neutral-700">Track provider responses and submit a review once work is completed.</p>
			</div>
			<a href="/services" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">New Booking <span aria-hidden="true">-></span></a>
		</header>

		<?php if (($bookings ?? []) === []): ?>
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">No bookings yet.</h3>
				<p class="mt-2 text-sm text-neutral-600">Browse available skills and create your first booking request.</p>
			</div>
		<?php else: ?>
			<div class="overflow-x-auto rounded-3xl border border-neutral-200 bg-white shadow-soft">
				<table class="min-w-full divide-y divide-neutral-200 text-sm">
					<thead class="bg-surface-page text-left text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-600">
						<tr>
							<th class="px-4 py-3">Service</th>
							<th class="px-4 py-3">Provider</th>
							<th class="px-4 py-3">Status</th>
							<th class="px-4 py-3">Payment</th>
							<th class="px-4 py-3">Action</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-neutral-200">
						<?php foreach ($bookings as $booking): ?>
							<?php
							$status = (string) ($booking['status'] ?? 'pending');
							$paymentStatus = strtolower((string) ($booking['payment_status'] ?? 'unpaid'));
							$canPay = $status === 'accepted' && $paymentStatus !== 'paid';
							?>
							<tr>
								<td class="px-4 py-3 font-semibold text-neutral-900"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="px-4 py-3 text-neutral-700"><?php echo htmlspecialchars((string) ($booking['provider_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="px-4 py-3">
									<span class="inline-flex items-center rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700"><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
								</td>
								<td class="px-4 py-3">
									<?php if ($paymentStatus === 'paid'): ?>
										<span class="inline-flex items-center rounded-full border border-success-200 bg-success-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-success-700">Paid</span>
									<?php else: ?>
										<span class="inline-flex items-center rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-neutral-600">Unpaid</span>
									<?php endif; ?>
								</td>
								<td class="px-4 py-3">
									<?php if ($status === 'completed'): ?>
										<a class="inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700" href="/reviews/create?booking_id=<?php echo (int) $booking['id']; ?>">Add review <span aria-hidden="true">-></span></a>
									<?php elseif ($canPay): ?>
										<a class="inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700" href="/bookings/<?php echo (int) $booking['id']; ?>/checkout">Pay now <span aria-hidden="true">-></span></a>
									<?php elseif ($status === 'accepted' && $paymentStatus === 'paid'): ?>
										<span class="text-xs font-bold uppercase tracking-wide text-neutral-500">Waiting completion</span>
									<?php elseif (in_array($status, ['pending', 'accepted'], true)): ?>
										<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
											<?php echo csrf_field(); ?>
											<input type="hidden" name="_method" value="PATCH">
											<input type="hidden" name="status" value="cancelled">
											<button type="submit" class="inline-flex items-center rounded-full border border-danger-300 bg-white px-4 py-2 text-xs font-extrabold text-danger-700 transition hover:bg-danger-50">Cancel booking</button>
										</form>
									<?php elseif (in_array($status, ['cancelled', 'rejected'], true)): ?>
										<span class="text-xs font-bold uppercase tracking-wide text-neutral-500">Closed</span>
									<?php else: ?>
										<span class="text-xs font-bold uppercase tracking-wide text-neutral-500">Waiting completion</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</section>
