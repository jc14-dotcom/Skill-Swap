<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Bookings</span>
				<h1 class="ss-page-title">Booking Overview</h1>
				<p class="ss-muted-block" style="margin-top: 0.45rem;">Monitor both seeker and provider booking queues in one place.</p>
			</div>
		</div>

		<div class="ss-dual-cards">
			<div class="ss-page-card">
				<h2 style="font-size: 1.8rem; margin-bottom: 0.7rem;">As Seeker</h2>
				<?php if (($seekerBookings ?? []) === []): ?>
					<div class="ss-empty-card">
						<p>No bookings yet.</p>
					</div>
				<?php else: ?>
					<div class="ss-table-shell">
						<table class="ss-table">
							<thead>
								<tr>
									<th>Service</th>
									<th>Provider</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($seekerBookings as $booking): ?>
									<tr>
										<td><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars((string) $booking['provider_name'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><span class="ss-chip"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>

			<div class="ss-page-card">
				<h2 style="font-size: 1.8rem; margin-bottom: 0.7rem;">As Provider</h2>
				<?php if (($providerBookings ?? []) === []): ?>
					<div class="ss-empty-card">
						<p>No incoming booking requests.</p>
					</div>
				<?php else: ?>
					<div class="ss-table-shell">
						<table class="ss-table">
							<thead>
								<tr>
									<th>Service</th>
									<th>Seeker</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($providerBookings as $booking): ?>
									<tr>
										<td><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars((string) $booking['seeker_name'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><span class="ss-chip"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
