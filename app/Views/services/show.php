<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<section class="pt-4 pb-14 md:pt-6 md:pb-16">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
			<div class="rounded-3xl border border-neutral-300 bg-surface-card px-6 py-12 text-center shadow-soft">
				<h2 class="text-2xl font-heading font-extrabold text-neutral-950">Service not found.</h2>
				<p class="mt-2 text-base text-neutral-700">The requested service may have been removed or is no longer available.</p>
				<a href="/services" class="mt-4 inline-flex items-center justify-center gap-2 rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:bg-brand-600">Back to marketplace <span aria-hidden="true">-></span></a>
			</div>
		</div>
	</section>
<?php else: ?>
	<?php $providerImage = profile_image_url((string) ($service['provider_profile_picture'] ?? '')); ?>
	<?php $viewer = auth_user(); ?>
	<?php $viewerId = is_array($viewer) ? (int) ($viewer['id'] ?? 0) : 0; ?>
	<?php $isOwnService = $viewerId > 0 && $viewerId === (int) ($service['user_id'] ?? 0); ?>
	<?php $providerBookings = is_array($providerBookings ?? null) ? $providerBookings : []; ?>
	<?php $bookingActionRedirect = (string) ($bookingActionRedirect ?? '/services/' . (int) ($service['id'] ?? 0)); ?>
	<?php $defaultAvatarImage = profile_image_url(''); ?>
	<?php
	$outlineButtonClasses = 'inline-flex items-center justify-center rounded-full border border-neutral-400 bg-white px-4 py-2 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700';
	$primaryButtonClasses = 'inline-flex items-center justify-center gap-2 rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600';
	$dangerButtonClasses = 'inline-flex items-center justify-center rounded-full border border-danger-300 bg-white px-4 py-2 text-sm font-extrabold text-danger-700 transition hover:bg-danger-50';
	$smallOutlineButtonClasses = 'inline-flex items-center justify-center rounded-full border border-neutral-400 bg-white px-3 py-2 text-xs font-extrabold text-neutral-800 transition hover:border-brand-500 hover:text-brand-700';
	?>
	<section class="pt-4 pb-14 md:pt-6 md:pb-16">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-6">
			<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
					<div class="min-w-0">
						<span class="inline-flex text-xs font-extrabold uppercase tracking-[0.2em] text-brand-500">Service Detail</span>
						<h1 class="mt-2 text-4xl font-heading font-extrabold leading-tight text-neutral-950 md:text-5xl"><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
						<div class="mt-3 flex flex-wrap items-center gap-3">
							<span class="inline-flex items-center rounded-full border border-neutral-200 bg-white px-3 py-1 text-xs font-bold uppercase tracking-wide text-neutral-600">Status: <?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></span>
							<span class="text-xs font-extrabold uppercase tracking-[0.16em] text-neutral-500">Price</span>
							<span class="text-base font-extrabold text-neutral-900"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
						</div>
						<div class="mt-4 flex flex-wrap items-center gap-4">
							<div class="flex items-center gap-3">
								<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="h-11 w-11 rounded-full border-2 border-brand-200 bg-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
								<div>
									<p class="text-xs font-extrabold uppercase tracking-[0.2em] text-neutral-500">Provider</p>
									<p class="text-sm font-semibold text-neutral-700"><?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></p>
								</div>
							</div>
						</div>
						<p class="mt-5 whitespace-pre-line text-base leading-8 text-neutral-700"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
					</div>
				</div>

				<div class="mt-6 flex flex-wrap items-center gap-3">
					<?php if (auth_check()): ?>
						<?php if (!$isOwnService): ?>
							<a href="/bookings/create?service_id=<?php echo (int) $service['id']; ?>" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Book this service <span aria-hidden="true">-></span></a>
							<a href="/services" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Back to marketplace</a>
						<?php else: ?>
							<?php if (($canManage ?? false) === true): ?>
								<a href="/services/<?php echo (int) $service['id']; ?>/edit" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Edit service</a>
								<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');">
									<?php echo csrf_field(); ?>
									<input type="hidden" name="_method" value="DELETE">
									<button type="submit" class="<?php echo htmlspecialchars($dangerButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Delete service</button>
								</form>
							<?php endif; ?>
							<a href="/services" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Back to marketplace</a>
						<?php endif; ?>
					<?php else: ?>
						<a href="/login" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Login to book</a>
						<a href="/services" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Back to marketplace</a>
					<?php endif; ?>
				</div>
			</header>

			<?php if (($canManage ?? false) === true): ?>
				<div class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(18rem,0.5fr)]">
					<section class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft md:p-6">
						<div class="flex flex-wrap items-start justify-between gap-3">
							<div>
								<span class="inline-flex text-xs font-extrabold uppercase tracking-[0.2em] text-brand-500">Provider Requests</span>
								<h2 class="mt-1 text-xl font-heading font-extrabold text-neutral-950">Bookings for this service</h2>
								<p class="mt-2 text-sm text-neutral-600">Review requests and update booking status.</p>
							</div>
							<a href="/bookings/provider" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Open full provider schedule</a>
						</div>

						<?php if ($providerBookings === []): ?>
							<div class="mt-4 rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-6 text-center">
								<p class="text-sm text-neutral-600">No one has booked this skill yet.</p>
							</div>
						<?php else: ?>
							<div class="mt-4 overflow-x-auto rounded-2xl border border-neutral-200">
								<table class="min-w-full w-full table-auto divide-y divide-neutral-200 text-sm">
									<thead class="bg-surface-page text-left text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-600">
										<tr>
											<th class="px-4 py-3">Seeker</th>
											<th class="px-4 py-3">Preferred Schedule</th>
											<th class="px-4 py-3">Status</th>
											<th class="px-4 py-3">Customer Note</th>
											<th class="px-4 py-3 text-right">Action</th>
										</tr>
									</thead>
									<tbody class="divide-y divide-neutral-200 bg-white">
										<?php foreach ($providerBookings as $booking): ?>
											<?php
											$currentStatus = strtolower((string) ($booking['status'] ?? 'pending'));
											$preferredSchedule = format_datetime((string) ($booking['scheduled_at'] ?? ''), 'M d, Y h:i A');
											$bookingMessage = trim((string) ($booking['message'] ?? ''));
											$allowedStatuses = [];
											$actionLabels = [
												'accepted' => 'Accept',
												'rejected' => 'Reject',
												'completed' => 'Complete',
												'cancelled' => 'Cancel',
											];
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
											$seekerImage = profile_image_url((string) ($booking['seeker_profile_picture'] ?? ''));

											if ($currentStatus === 'pending') {
												$allowedStatuses = ['accepted', 'rejected'];
											} elseif ($currentStatus === 'accepted') {
												$allowedStatuses = ['completed', 'cancelled'];
											}
											?>
											<tr class="align-top transition hover:bg-surface-page/70">
												<td class="px-4 py-4">
													<div class="flex items-center gap-3">
														<img src="<?php echo htmlspecialchars($seekerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? 'Seeker'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="h-9 w-9 rounded-full border border-brand-200 bg-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
														<div class="min-w-0">
															<p class="text-sm font-semibold text-neutral-900"><?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></p>
															<p class="text-xs text-neutral-500">Request #<?php echo (int) ($booking['id'] ?? 0); ?></p>
														</div>
													</div>
												</td>
												<td class="px-4 py-4 whitespace-nowrap text-neutral-700"><?php echo htmlspecialchars($preferredSchedule, ENT_QUOTES, 'UTF-8'); ?></td>
												<td class="px-4 py-4">
													<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wide <?php echo htmlspecialchars((string) ($statusChipClasses[$currentStatus] ?? $statusChipClasses['pending']), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($statusLabels[$currentStatus] ?? ucfirst($currentStatus)), ENT_QUOTES, 'UTF-8'); ?></span>
												</td>
												<td class="px-4 py-4 text-neutral-700">
													<span class="block max-w-[20rem] break-words leading-6"><?php echo htmlspecialchars($bookingMessage !== '' ? $bookingMessage : '-', ENT_QUOTES, 'UTF-8'); ?></span>
												</td>
												<td class="px-4 py-4 text-right">
													<div class="flex flex-wrap items-center justify-end gap-2">
														<?php if ($allowedStatuses === []): ?>
															<span class="text-xs font-bold uppercase tracking-wide text-neutral-500">No further actions</span>
														<?php else: ?>
															<?php foreach ($allowedStatuses as $nextStatus): ?>
																<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
																	<?php echo csrf_field(); ?>
																	<input type="hidden" name="_method" value="PATCH">
																	<input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus, ENT_QUOTES, 'UTF-8'); ?>">
																	<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($bookingActionRedirect, ENT_QUOTES, 'UTF-8'); ?>">
																	<button type="submit" class="<?php echo htmlspecialchars($smallOutlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($actionLabels[$nextStatus] ?? ucfirst($nextStatus)), ENT_QUOTES, 'UTF-8'); ?></button>
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
					</section>

					<aside class="space-y-4">
						<section class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
							<span class="inline-flex w-fit rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-neutral-600">Guidance</span>
							<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Review details before confirming a booking.</h2>
							<p class="mt-2 text-sm leading-7 text-neutral-600">Strong outcomes come from clear expectations, communication rhythm, and agreed scope.</p>
							<ul class="mt-3 grid gap-2 text-sm text-neutral-700">
								<li>Validate timeline and deliverables early.</li>
								<li>Use messages to confirm milestones.</li>
								<li>Submit review after completion for trust.</li>
							</ul>
						</section>
					</aside>
				</div>
			<?php else: ?>
				<section class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft md:p-6">
					<span class="inline-flex w-fit rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-neutral-600">Guidance</span>
					<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Review details before confirming a booking.</h2>
					<p class="mt-2 text-sm leading-7 text-neutral-600">Strong outcomes come from clear expectations, communication rhythm, and agreed scope.</p>
					<ul class="mt-3 grid gap-2 text-sm text-neutral-700">
						<li>Validate timeline and deliverables early.</li>
						<li>Use messages to confirm milestones.</li>
						<li>Submit review after completion for trust.</li>
					</ul>
				</section>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>
