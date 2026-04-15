<?php declare(strict_types=1); ?>

<?php
$selectedService = is_array($service ?? null) ? $service : null;
$selectedServiceId = is_array($selectedService) ? (int) ($selectedService['id'] ?? 0) : 0;
?>

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<?php if (is_array($selectedService)): ?>
			<?php $providerImage = profile_image_url((string) ($selectedService['provider_profile_picture'] ?? '')); ?>

			<div class="ss-page-head">
				<div>
					<span class="ss-section-label">Confirm Booking</span>
					<h1 class="ss-page-title">Book This Skill</h1>
					<p class="ss-muted-block" style="margin-top: 0.5rem;">You selected a skill. Review the details then confirm your booking.</p>
				</div>
			</div>

			<div class="ss-split-shell">
				<form action="/bookings" method="post" class="ss-form-shell ss-split-main">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="service_id" value="<?php echo $selectedServiceId; ?>">

					<div class="ss-page-card" style="padding: 1.1rem;">
						<div>
							<h3 style="font-size: 1.35rem;"><?php echo htmlspecialchars((string) ($selectedService['title'] ?? 'Skill Service'), ENT_QUOTES, 'UTF-8'); ?></h3>
							<div class="ss-provider-row" style="margin-top: 0.45rem;">
								<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($selectedService['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="ss-provider-avatar">
								<p class="ss-service-meta">By <?php echo htmlspecialchars((string) ($selectedService['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						</div>

						<p class="ss-service-desc" style="margin-top: 0.85rem;"><?php echo htmlspecialchars((string) ($selectedService['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="ss-price-chip" style="margin-top: 0.8rem;"><?php echo htmlspecialchars(format_currency((float) ($selectedService['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></p>
					</div>

					<div class="ss-actions">
						<button type="submit" class="ss-btn-soft">Confirm Booking <span class="ss-arrow">-></span></button>
						<a href="/services" class="ss-btn-outline">Back to skills</a>
						<a href="/services/<?php echo $selectedServiceId; ?>" class="ss-btn-outline">View full details</a>
					</div>
				</form>

				<aside class="ss-split-aside">
					<section class="ss-accent-panel">
						<span class="ss-accent-tag">Next Step</span>
						<h2>Provider review starts after confirmation.</h2>
						<p>Once submitted, your request appears in the provider queue for approval or rejection.</p>
						<ul class="ss-accent-list">
							<li>Watch status updates in My Bookings.</li>
							<li>Use Messages after provider responds.</li>
							<li>Leave a review after completion.</li>
						</ul>
					</section>
				</aside>
			</div>
		<?php else: ?>
			<div class="ss-empty-card">
				<h3>Select a skill first.</h3>
				<p>Open the skill board, pick a service, then continue booking.</p>
				<a href="/services" class="ss-btn" style="margin-top: 1rem;">Open skill board <span class="ss-arrow">-></span></a>
			</div>
		<?php endif; ?>
	</div>
</section>
