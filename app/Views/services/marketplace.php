<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Marketplace</span>
				<h1 class="ss-page-title">Find Skills To Learn Or Swap</h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">Browse active offers, book what you need, or publish your own service.</p>
			</div>
			<div class="ss-actions">
				<a href="/bookings/create" class="ss-btn-outline">Seek skill</a>
				<a href="/services/create" class="ss-btn-soft">Offer skill <span class="ss-arrow">-></span></a>
			</div>
		</div>

		<?php if (($services ?? []) === []): ?>
			<div class="ss-empty-card">
				<h3>No active services yet.</h3>
				<p>Be the first to offer a skill and start swapping with the community.</p>
				<a href="/services/create" class="ss-btn" style="margin-top: 1rem;">Create first service <span class="ss-arrow">-></span></a>
			</div>
		<?php else: ?>
			<div class="ss-grid-3">
				<?php foreach ($services as $service): ?>
					<article class="ss-service-card">
						<div>
							<h3><?php echo htmlspecialchars((string) ($service['title'] ?? 'Skill Service'), ENT_QUOTES, 'UTF-8'); ?></h3>
							<p class="ss-service-meta">By <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?></p>
						</div>

						<p class="ss-service-desc"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="ss-service-foot">
							<span class="ss-price-chip"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
							<div class="ss-actions" style="gap: 0.45rem;">
								<a href="/services/<?php echo (int) ($service['id'] ?? 0); ?>" class="ss-mini-link">View</a>
								<a href="/bookings/create?service_id=<?php echo (int) ($service['id'] ?? 0); ?>" class="ss-mini-link">Book</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="ss-page-card">
			<h2 style="font-size: 1.4rem; margin-bottom: 0.65rem;">Inside System Links</h2>
			<div class="ss-actions">
				<a href="/home" class="ss-btn-outline">Member home</a>
				<a href="/services/mine" class="ss-btn-outline">My services</a>
				<a href="/bookings/mine" class="ss-btn-outline">My bookings</a>
				<a href="/bookings/provider" class="ss-btn-outline">Provider queue</a>
			</div>
		</div>
	</div>
</section>
