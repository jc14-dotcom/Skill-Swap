<?php declare(strict_types=1); ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
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
			<div class="overflow-x-auto rounded-3xl border border-neutral-200 bg-white shadow-soft">
				<table class="min-w-full w-full table-auto divide-y divide-neutral-200 text-sm">
					<thead class="bg-surface-page text-left text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-600">
						<tr>
							<th class="px-4 py-3">Service</th>
							<th class="px-4 py-3">Seeker</th>
							<th class="px-4 py-3">Preferred Schedule</th>
							<th class="px-4 py-3">Status</th>
							<th class="px-4 py-3">Customer Note</th>
							<th class="px-4 py-3 text-right">Action</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-neutral-200 bg-white">
						<?php foreach ($bookings as $booking): ?>
							<?php
							$currentStatus = strtolower((string) ($booking['status'] ?? 'pending'));
							$preferredSchedule = format_datetime((string) ($booking['scheduled_at'] ?? ''), 'M d, Y h:i A');
							$bookingMessage = trim((string) ($booking['message'] ?? ''));
							$allowedStatuses = [];
							$statusLabels = [
								'pending' => 'Pending',
								'accepted' => 'Accepted',
								'rejected' => 'Rejected',
								'completed' => 'Completed',
								'cancelled' => 'Cancelled',
							];
							$statusChipClasses = [
								'pending' => 'border-neutral-200 bg-neutral-50 text-neutral-700',
								'accepted' => 'border-brand-200 bg-brand-50 text-brand-700',
								'rejected' => 'border-danger-200 bg-danger-50 text-danger-700',
								'completed' => 'border-brand-200 bg-brand-50 text-brand-700',
								'cancelled' => 'border-danger-200 bg-danger-50 text-danger-700',
							];

							if ($currentStatus === 'pending') {
								$allowedStatuses = ['accepted', 'rejected'];
							} elseif ($currentStatus === 'accepted') {
								$allowedStatuses = ['completed', 'cancelled'];
							}
							?>
							<tr class="align-top transition hover:bg-surface-page/70">
								<td class="px-4 py-4 font-semibold text-neutral-900"><?php echo htmlspecialchars((string) ($booking['service_title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="px-4 py-4 text-neutral-700"><?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="px-4 py-4 whitespace-nowrap text-neutral-700"><?php echo htmlspecialchars($preferredSchedule, ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="px-4 py-4">
									<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide <?php echo htmlspecialchars((string) ($statusChipClasses[$currentStatus] ?? $statusChipClasses['pending']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($statusLabels[$currentStatus] ?? ucfirst($currentStatus)), ENT_QUOTES, 'UTF-8'); ?></span>
								</td>
								<td class="px-4 py-4 text-neutral-700">
									<span class="block max-w-[22rem] break-words leading-6"><?php echo htmlspecialchars($bookingMessage !== '' ? $bookingMessage : '—', ENT_QUOTES, 'UTF-8'); ?></span>
								</td>
								<td class="px-4 py-4">
									<div class="flex flex-wrap items-center justify-end gap-2">
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
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</section>
