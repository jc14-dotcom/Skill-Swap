<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Seeker Zone</span>
				<h1 class="ss-page-title">My Booking Requests</h1>
				<p class="ss-muted-block" style="margin-top: 0.4rem;">Track provider responses and submit a review once work is completed.</p>
			</div>
			<a href="/bookings/create" class="ss-btn">New Booking <span class="ss-arrow">-></span></a>
		</div>

		<?php if (($bookings ?? []) === []): ?>
			<div class="ss-empty-card">
				<h3>No bookings yet.</h3>
				<p>Browse the marketplace and create your first booking request.</p>
			</div>
		<?php else: ?>
			<div class="ss-table-shell">
				<table class="ss-table">
					<thead>
						<tr>
							<th>Service</th>
							<th>Provider</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($bookings as $booking): ?>
							<?php $status = (string) ($booking['status'] ?? 'pending'); ?>
							<tr>
								<td><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars((string) ($booking['provider_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								<td><span class="ss-chip"><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></td>
								<td>
									<?php if ($status === 'completed'): ?>
										<a class="ss-mini-link" href="/reviews/create?booking_id=<?php echo (int) $booking['id']; ?>">Add review <span class="ss-arrow">-></span></a>
									<?php elseif (in_array($status, ['pending', 'accepted'], true)): ?>
										<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
											<?php echo csrf_field(); ?>
											<input type="hidden" name="_method" value="PATCH">
											<input type="hidden" name="status" value="cancelled">
											<button type="submit" class="ss-btn-outline">Cancel booking</button>
										</form>
									<?php elseif (in_array($status, ['cancelled', 'rejected'], true)): ?>
										<span class="ss-subtle">Closed</span>
									<?php else: ?>
										<span class="ss-subtle">Waiting completion</span>
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
