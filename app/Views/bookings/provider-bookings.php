<?php declare(strict_types=1); ?>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Provider Zone
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Provider Booking Schedule</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Review customer requests, check preferred schedule, and accept or reject bookings.</p>
		</header>

		<?php if (($bookings ?? []) === []): ?>
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">No incoming booking requests.</h3>
				<p class="mt-2 text-sm text-neutral-600">Once seekers submit requests to your services, they will appear here.</p>
			</div>
		<?php else: ?>
			<div class="grid gap-4 lg:grid-cols-2">
				<?php foreach ($bookings as $booking): ?>
					<?php
					$currentStatus = (string) ($booking['status'] ?? 'pending');
					$preferredSchedule = format_datetime((string) ($booking['scheduled_at'] ?? ''), 'M d, Y h:i A');
					$bookingMessage = trim((string) ($booking['message'] ?? ''));
					$allowedStatuses = [];
					$statusLabels = [
						'accepted' => 'Accept Customer',
						'rejected' => 'Reject Customer',
						'completed' => 'Mark Completed',
						'cancelled' => 'Cancel Booking',
					];

					if ($currentStatus === 'pending') {
						$allowedStatuses = ['accepted', 'rejected'];
					} elseif ($currentStatus === 'accepted') {
						$allowedStatuses = ['completed', 'cancelled'];
					}
					?>
					<article class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
						<span class="inline-flex items-center rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">Status: <?php echo htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8'); ?></span>
						<h3 class="mt-4 text-xl font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="mt-2 text-sm text-neutral-700">Seeker: <?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="mt-1 text-sm text-neutral-700">Preferred Schedule: <?php echo htmlspecialchars($preferredSchedule, ENT_QUOTES, 'UTF-8'); ?></p>
						<?php if ($bookingMessage !== ''): ?>
							<p class="mt-1 text-sm text-neutral-700">Customer Note: <?php echo htmlspecialchars($bookingMessage, ENT_QUOTES, 'UTF-8'); ?></p>
						<?php endif; ?>

						<div class="mt-4 flex flex-wrap gap-2">
							<?php if ($allowedStatuses === []): ?>
								<span class="text-xs font-bold uppercase tracking-wide text-neutral-500">No further actions</span>
							<?php else: ?>
								<?php foreach ($allowedStatuses as $nextStatus): ?>
									<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
										<?php echo csrf_field(); ?>
										<input type="hidden" name="_method" value="PATCH">
										<input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus, ENT_QUOTES, 'UTF-8'); ?>">
										<button type="submit" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700"><?php echo htmlspecialchars((string) ($statusLabels[$nextStatus] ?? ucfirst($nextStatus)), ENT_QUOTES, 'UTF-8'); ?></button>
									</form>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
