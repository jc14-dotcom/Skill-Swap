<?php declare(strict_types=1); ?>

<?php $serviceId = is_array($service ?? null) ? (int) $service['id'] : (int) old_input('service_id', 0); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Booking Request</span>
				<h1 class="ss-page-title">Book A Skill Service</h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">Send a clear request so providers can review and respond quickly.</p>
			</div>
		</div>

		<div class="ss-split-shell">
			<form action="/bookings" method="post" class="ss-form-shell ss-split-main">
				<?php echo csrf_field(); ?>

				<div class="ss-field">
					<label for="service_id">Service ID</label>
					<input id="service_id" name="service_id" type="number" min="1" required value="<?php echo $serviceId > 0 ? $serviceId : ''; ?>" class="ss-input">
					<?php if (is_array($service ?? null)): ?>
						<p class="ss-subtle">Booking for: <?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></p>
					<?php endif; ?>
				</div>

				<div class="ss-field">
					<label for="scheduled_at">Preferred Schedule (optional)</label>
					<input id="scheduled_at" name="scheduled_at" type="datetime-local" value="<?php echo htmlspecialchars((string) old_input('scheduled_at'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
				</div>

				<div class="ss-field">
					<label for="message">Message to provider (optional)</label>
					<textarea id="message" name="message" rows="5" class="ss-textarea"><?php echo htmlspecialchars((string) old_input('message'), ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="ss-actions">
					<button type="submit" class="ss-btn-soft">Submit Booking <span class="ss-arrow">-></span></button>
					<a href="/services" class="ss-btn-outline">Back to services</a>
				</div>
			</form>

			<aside class="ss-split-aside">
				<section class="ss-accent-panel">
					<span class="ss-accent-tag">Booking Tips</span>
					<h2>Make your request easy to approve.</h2>
					<p>Providers respond faster when the request is specific, realistic, and aligned with the listed service.</p>
					<ul class="ss-accent-list">
						<li>Include your target outcome and deadline.</li>
						<li>Mention key files, links, or references.</li>
						<li>Suggest schedule options for smoother coordination.</li>
					</ul>
				</section>

				<section class="ss-page-card ss-mini-info-card">
					<h3 style="font-size: 1rem;">Quick Checklist</h3>
					<div class="ss-mini-info-row"><span>Service reference</span><strong>ID ready</strong></div>
					<div class="ss-mini-info-row"><span>Schedule details</span><strong>Optional</strong></div>
					<div class="ss-mini-info-row"><span>Extra context</span><strong>Recommended</strong></div>
				</section>
			</aside>
		</div>
	</div>
</section>
