<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<section class="py-14">
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
	<section class="py-14">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
			<div class="grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.65fr)]">
				<div class="rounded-3xl border border-neutral-300 bg-surface-card p-6 shadow-soft md:p-8">
					<div class="flex flex-wrap items-start justify-between gap-4">
						<div>
							<span class="inline-flex text-xs font-extrabold uppercase tracking-[0.2em] text-brand-500">Service Detail</span>
							<h1 class="mt-2 text-4xl font-heading font-extrabold leading-tight text-neutral-950 md:text-5xl"><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
							<div class="mt-4 flex items-center gap-3">
								<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="h-11 w-11 rounded-full border-2 border-brand-200 bg-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
								<p class="text-sm font-bold text-neutral-700">Provider: <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						</div>

						<div class="grid gap-2">
							<span class="inline-flex w-fit items-center rounded-full border border-neutral-300 bg-neutral-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-neutral-700">Status: <?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></span>
							<p class="text-right text-lg font-extrabold text-brand-700"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></p>
						</div>
					</div>

					<p class="mt-6 whitespace-pre-line text-base leading-8 text-neutral-700"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

					<div class="mt-6 flex flex-wrap items-center gap-3">
						<?php if (auth_check()): ?>
							<?php if (!$isOwnService): ?>
								<a href="/bookings/create?service_id=<?php echo (int) $service['id']; ?>" class="<?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Book this service <span aria-hidden="true">-></span></a>
							<?php else: ?>
								<span class="text-sm font-semibold text-neutral-600">You cannot book your own offered service.</span>
							<?php endif; ?>
						<?php else: ?>
							<a href="/login" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Login to book</a>
						<?php endif; ?>

						<a href="/services" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Back to marketplace</a>
					</div>

					<?php if (($canManage ?? false) === true): ?>
						<div class="mt-4 flex flex-wrap items-center gap-3">
							<a href="/services/<?php echo (int) $service['id']; ?>/edit" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Edit service</a>
							<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="_method" value="DELETE">
								<button type="submit" class="<?php echo htmlspecialchars($dangerButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Delete service</button>
							</form>
						</div>

						<section class="mt-6 rounded-2xl border border-neutral-300 bg-neutral-50 p-4 shadow-soft">
							<div class="mb-3 flex flex-wrap items-start justify-between gap-3">
								<div>
									<span class="inline-flex text-xs font-extrabold uppercase tracking-[0.2em] text-brand-500">Provider Requests</span>
									<h2 class="mt-1 text-xl font-heading font-extrabold text-neutral-950">People Who Booked This Skill</h2>
								</div>
								<a href="/bookings/provider" class="<?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Open full provider schedule</a>
							</div>

							<?php if ($providerBookings === []): ?>
								<div class="rounded-2xl border border-neutral-300 bg-white px-4 py-6 text-center">
									<p class="text-sm text-neutral-600">No one has booked this skill yet.</p>
								</div>
							<?php else: ?>
								<div class="grid gap-3">
									<?php foreach ($providerBookings as $booking): ?>
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
										$seekerImage = profile_image_url((string) ($booking['seeker_profile_picture'] ?? ''));

										if ($currentStatus === 'pending') {
											$allowedStatuses = ['accepted', 'rejected'];
										} elseif ($currentStatus === 'accepted') {
											$allowedStatuses = ['completed', 'cancelled'];
										}

										$statusChipClass = 'border-neutral-300 bg-neutral-100 text-neutral-700';
										if ($currentStatus === 'pending') {
											$statusChipClass = 'border-warning-200 bg-warning-50 text-warning-700';
										} elseif ($currentStatus === 'accepted') {
											$statusChipClass = 'border-info-200 bg-info-50 text-info-700';
										} elseif ($currentStatus === 'completed') {
											$statusChipClass = 'border-success-200 bg-success-50 text-success-700';
										} elseif ($currentStatus === 'rejected' || $currentStatus === 'cancelled') {
											$statusChipClass = 'border-danger-200 bg-danger-50 text-danger-700';
										}
										?>
										<article class="rounded-2xl border border-neutral-300 bg-white p-4 shadow-soft">
											<span class="inline-flex w-fit items-center rounded-full border px-3 py-1 text-xs font-extrabold uppercase tracking-wide <?php echo htmlspecialchars($statusChipClass, ENT_QUOTES, 'UTF-8'); ?>">Status: <?php echo htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8'); ?></span>
											<div class="mt-2 flex items-center gap-3">
												<img src="<?php echo htmlspecialchars($seekerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? 'Seeker'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="h-9 w-9 rounded-full border-2 border-brand-200 bg-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
												<p class="text-sm font-semibold text-neutral-700">Seeker: <?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></p>
											</div>
											<p class="mt-1 text-sm text-neutral-700">Preferred schedule: <?php echo htmlspecialchars($preferredSchedule, ENT_QUOTES, 'UTF-8'); ?></p>
											<?php if ($bookingMessage !== ''): ?>
												<p class="mt-1 text-sm text-neutral-700">Customer note: <?php echo htmlspecialchars($bookingMessage, ENT_QUOTES, 'UTF-8'); ?></p>
											<?php endif; ?>

											<div class="mt-3 flex flex-wrap items-center gap-2">
												<?php if ($allowedStatuses === []): ?>
													<span class="text-xs font-semibold text-neutral-600">No further actions</span>
												<?php else: ?>
													<?php foreach ($allowedStatuses as $nextStatus): ?>
														<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
															<?php echo csrf_field(); ?>
															<input type="hidden" name="_method" value="PATCH">
															<input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus, ENT_QUOTES, 'UTF-8'); ?>">
															<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($bookingActionRedirect, ENT_QUOTES, 'UTF-8'); ?>">
															<button type="submit" class="<?php echo htmlspecialchars($smallOutlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($statusLabels[$nextStatus] ?? ucfirst($nextStatus)), ENT_QUOTES, 'UTF-8'); ?></button>
														</form>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
										</article>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</section>
					<?php endif; ?>
				</div>

				<aside class="grid gap-4">
					<section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 via-brand-600 to-brandAccent-tintSoft p-5 text-white shadow-card">
						<div aria-hidden="true" class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full border border-overlay-white35"></div>
						<span class="inline-flex w-fit rounded-full border border-overlay-white35 bg-overlay-white18 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-50">Service Snapshot</span>
						<h2 class="mt-3 text-2xl font-heading font-extrabold leading-tight text-white">Review details before confirming a booking.</h2>
						<p class="mt-2 text-sm leading-7 text-brand-100">Strong outcomes come from clear expectations, communication rhythm, and agreed scope.</p>
						<ul class="mt-3 grid gap-2 text-sm text-brand-50">
							<li>Validate timeline and deliverables early.</li>
							<li>Use messages to confirm milestones.</li>
							<li>Submit review after completion for trust.</li>
						</ul>
					</section>

					<section class="rounded-3xl border border-neutral-300 bg-surface-card p-5 shadow-soft">
						<h3 class="text-lg font-heading font-extrabold text-neutral-950">Quick Facts</h3>
						<div class="mt-3 flex items-center justify-between gap-3 border-t border-neutral-200 pt-3 text-sm text-neutral-700"><span>Service ID</span><strong class="font-extrabold text-neutral-900">#<?php echo (int) $service['id']; ?></strong></div>
						<div class="mt-2 flex items-center justify-between gap-3 border-t border-neutral-200 pt-3 text-sm text-neutral-700">
							<span>Provider</span>
							<strong class="inline-flex items-center gap-2 text-right text-sm font-extrabold text-neutral-900">
								<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Provider profile picture" class="h-8 w-8 rounded-full border border-brand-200 bg-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
								<?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?>
							</strong>
						</div>
						<div class="mt-2 flex items-center justify-between gap-3 border-t border-neutral-200 pt-3 text-sm text-neutral-700"><span>Current status</span><strong class="font-extrabold text-neutral-900"><?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
					</section>
				</aside>
			</div>
		</div>
	</section>
<?php endif; ?>
