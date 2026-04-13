<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Messaging</span>
				<h1 class="ss-page-title">Conversations</h1>
				<p class="ss-muted-block" style="margin-top: 0.4rem;">Talk directly with your booking counterpart inside each booking thread.</p>
			</div>
		</div>

		<?php if (($conversations ?? []) === []): ?>
			<div class="ss-empty-card">
				<h3>No active conversations yet.</h3>
				<p>Once you have bookings, you can message the other member here.</p>
			</div>
		<?php else: ?>
			<div class="ss-booking-list">
				<?php foreach ($conversations as $conversation): ?>
					<?php
					$bookingId = (int) ($conversation['booking_id'] ?? 0);
					$unreadCount = (int) ($conversation['unread_count'] ?? 0);
					$status = (string) ($conversation['status'] ?? 'pending');
					?>
					<article class="ss-record-card">
						<span class="ss-chip">Status: <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
						<h3><?php echo htmlspecialchars((string) ($conversation['service_title'] ?? 'Booking'), ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="ss-muted-block">With: <?php echo htmlspecialchars((string) ($conversation['counterpart_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="ss-muted-block" style="margin-top: 0.3rem;">
							<?php echo htmlspecialchars((string) ($conversation['last_message_body'] ?? 'No messages yet.'), ENT_QUOTES, 'UTF-8'); ?>
						</p>

						<div class="ss-record-actions">
							<?php if ($unreadCount > 0): ?>
								<span class="ss-chip">Unread: <?php echo $unreadCount; ?></span>
							<?php else: ?>
								<span class="ss-subtle"><?php echo htmlspecialchars(format_datetime((string) ($conversation['last_message_at'] ?? ''), 'M d, Y H:i'), ENT_QUOTES, 'UTF-8'); ?></span>
							<?php endif; ?>
							<a href="/messages/<?php echo $bookingId; ?>" class="ss-mini-link">Open chat <span class="ss-arrow">-></span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
