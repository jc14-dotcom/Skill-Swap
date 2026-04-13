<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<section class="ss-section">
		<div class="ss-shell">
			<div class="ss-empty-card">
				<h2>Service not found.</h2>
				<p>The requested service may have been removed or is no longer available.</p>
				<a href="/services" class="ss-btn" style="margin-top: 1rem;">Back to marketplace <span class="ss-arrow">-></span></a>
			</div>
		</div>
	</section>
<?php else: ?>
	<section class="ss-section">
		<div class="ss-shell">
			<div class="ss-split-shell">
				<div class="ss-page-card ss-split-main">
					<div class="ss-page-head" style="margin-bottom: 1rem;">
						<div>
							<span class="ss-section-label">Service Detail</span>
							<h1 class="ss-page-title"><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
							<p class="ss-muted-block" style="margin-top: 0.6rem;">Provider: <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></p>
						</div>

						<div>
							<span class="ss-chip">Status: <?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></span>
							<p class="ss-price-chip" style="margin-top: 0.55rem;"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></p>
						</div>
					</div>

					<p class="ss-section-copy" style="white-space: pre-line;"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

					<div class="ss-actions" style="margin-top: 1.4rem;">
						<?php if (auth_check()): ?>
							<a href="/bookings/create?service_id=<?php echo (int) $service['id']; ?>" class="ss-btn-soft">Book this service <span class="ss-arrow">-></span></a>
						<?php else: ?>
							<a href="/login" class="ss-btn-outline">Login to book</a>
						<?php endif; ?>

						<a href="/services" class="ss-btn-outline">Back to marketplace</a>
					</div>

					<?php if (($canManage ?? false) === true): ?>
						<div class="ss-actions" style="margin-top: 0.9rem;">
							<a href="/services/<?php echo (int) $service['id']; ?>/edit" class="ss-btn-outline">Edit service</a>
							<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="_method" value="DELETE">
								<button type="submit" class="ss-btn-outline" style="border-color: #d07f8f; color: #8c2f48;">Delete service</button>
							</form>
						</div>
					<?php endif; ?>
				</div>

				<aside class="ss-split-aside">
					<section class="ss-accent-panel">
						<span class="ss-accent-tag">Service Snapshot</span>
						<h2>Review details before confirming a booking.</h2>
						<p>Strong outcomes come from clear expectations, communication rhythm, and agreed scope.</p>
						<ul class="ss-accent-list">
							<li>Validate timeline and deliverables early.</li>
							<li>Use messages to confirm milestones.</li>
							<li>Submit review after completion for trust.</li>
						</ul>
					</section>

					<section class="ss-page-card ss-mini-info-card">
						<h3 style="font-size: 1rem;">Quick Facts</h3>
						<div class="ss-mini-info-row"><span>Service ID</span><strong>#<?php echo (int) $service['id']; ?></strong></div>
						<div class="ss-mini-info-row"><span>Provider</span><strong><?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
						<div class="ss-mini-info-row"><span>Current status</span><strong><?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
					</section>
				</aside>
			</div>
		</div>
	</section>
<?php endif; ?>
