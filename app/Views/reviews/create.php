<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Feedback</span>
				<h1 class="ss-page-title">Submit A Booking Review</h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">Reviews are available after booking completion and visible for platform trust.</p>
			</div>
		</div>

		<div class="ss-split-shell">
			<form action="/reviews" method="post" class="ss-form-shell ss-split-main">
				<?php echo csrf_field(); ?>

				<div class="ss-field">
					<label for="booking_id">Booking ID</label>
					<input id="booking_id" name="booking_id" type="number" min="1" required value="<?php echo (int) old_input('booking_id', (int) ($bookingId ?? 0)); ?>" class="ss-input">
				</div>

				<div class="ss-field">
					<label for="rating">Rating</label>
					<?php $oldRating = (string) old_input('rating', '5'); ?>
					<select id="rating" name="rating" class="ss-select">
						<?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
							<option value="<?php echo $rating; ?>" <?php echo $oldRating === (string) $rating ? 'selected' : ''; ?>>
								<?php echo $rating; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="ss-field">
					<label for="comment">Comment (optional)</label>
					<textarea id="comment" name="comment" rows="5" class="ss-textarea"><?php echo htmlspecialchars((string) old_input('comment'), ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="ss-actions">
					<button type="submit" class="ss-btn-soft">Submit Review <span class="ss-arrow">-></span></button>
					<a href="/bookings/mine" class="ss-btn-outline">Back to my bookings</a>
				</div>
			</form>

			<aside class="ss-split-aside">
				<section class="ss-accent-panel">
					<span class="ss-accent-tag">Review Quality</span>
					<h2>Give feedback that helps the next member.</h2>
					<p>Balanced, specific reviews improve trust and help providers and seekers work better together.</p>
					<ul class="ss-accent-list">
						<li>Describe outcomes, not just opinions.</li>
						<li>Mention communication and delivery quality.</li>
						<li>Keep tone professional and constructive.</li>
					</ul>
				</section>

				<section class="ss-page-card ss-mini-info-card">
					<h3 style="font-size: 1rem;">Submission Snapshot</h3>
					<div class="ss-mini-info-row"><span>Booking reference</span><strong>#<?php echo (int) old_input('booking_id', (int) ($bookingId ?? 0)); ?></strong></div>
					<div class="ss-mini-info-row"><span>Selected rating</span><strong><?php echo htmlspecialchars($oldRating, ENT_QUOTES, 'UTF-8'); ?>/5</strong></div>
				</section>
			</aside>
		</div>
	</div>
</section>
