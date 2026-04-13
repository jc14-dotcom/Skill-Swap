<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Provider Zone</span>
				<h1 class="ss-page-title">Incoming Booking Requests</h1>
				<p class="ss-muted-block" style="margin-top: 0.45rem;">Update booking states based on the workflow policy and service readiness.</p>
			</div>
		</div>

		<?php if (($bookings ?? []) === []): ?>
			<div class="ss-empty-card">
				<h3>No incoming booking requests.</h3>
				<p>Once seekers submit requests to your services, they will appear here.</p>
			</div>
		<?php else: ?>
			<div class="ss-booking-list">
				<?php foreach ($bookings as $booking): ?>
					<?php
					$currentStatus = (string) ($booking['status'] ?? 'pending');
					$allowedStatuses = [];

					if ($currentStatus === 'pending') {
						$allowedStatuses = ['accepted', 'rejected'];
					} elseif ($currentStatus === 'accepted') {
						$allowedStatuses = ['completed', 'cancelled'];
					}
					?>
					<article class="ss-record-card">
						<span class="ss-chip">Status: <?php echo htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8'); ?></span>
						<h3><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="ss-muted-block">Seeker: <?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="ss-record-actions">
							<?php if ($allowedStatuses === []): ?>
								<span class="ss-subtle">No further actions</span>
							<?php else: ?>
								<?php foreach ($allowedStatuses as $nextStatus): ?>
									<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
										<?php echo csrf_field(); ?>
										<input type="hidden" name="_method" value="PATCH">
										<input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus, ENT_QUOTES, 'UTF-8'); ?>">
										<button type="submit" class="ss-btn-outline"><?php echo htmlspecialchars(ucfirst($nextStatus), ENT_QUOTES, 'UTF-8'); ?></button>
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
