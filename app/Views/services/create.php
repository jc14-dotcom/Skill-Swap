<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Provider Portal</span>
				<h1 class="ss-page-title">Create A New Skill Service</h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">Write clear deliverables and pricing so seekers can book with confidence.</p>
			</div>
		</div>

		<div class="ss-split-shell">
			<form action="/services" method="post" class="ss-form-shell ss-split-main">
				<?php echo csrf_field(); ?>

				<div class="ss-field">
					<label for="title">Service title</label>
					<input id="title" name="title" type="text" required value="<?php echo htmlspecialchars((string) old_input('title'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
				</div>

				<div class="ss-field">
					<label for="description">Description</label>
					<textarea id="description" name="description" rows="6" required class="ss-textarea"><?php echo htmlspecialchars((string) old_input('description'), ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="ss-field">
					<label for="price">Price</label>
					<input id="price" name="price" type="number" min="1" step="0.01" required value="<?php echo htmlspecialchars((string) old_input('price'), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
				</div>

				<div class="ss-actions">
					<button type="submit" class="ss-btn-soft">Save Service <span class="ss-arrow">-></span></button>
					<a href="/services/mine" class="ss-btn-outline">Cancel</a>
				</div>
			</form>

			<aside class="ss-split-aside">
				<section class="ss-accent-panel">
					<span class="ss-accent-tag">Provider Focus</span>
					<h2>Position your service for faster booking.</h2>
					<p>Strong service listings reduce back-and-forth messages and increase high-quality booking requests.</p>
					<ul class="ss-accent-list">
						<li>Use a title that states your exact offer.</li>
						<li>Outline clear deliverables and revision limits.</li>
						<li>Set a price that matches scope and turnaround.</li>
					</ul>
				</section>

				<section class="ss-page-card ss-mini-info-card">
					<h3 style="font-size: 1rem;">Listing Quality</h3>
					<div class="ss-mini-info-row"><span>Clear title</span><strong>High impact</strong></div>
					<div class="ss-mini-info-row"><span>Detailed scope</span><strong>Builds trust</strong></div>
					<div class="ss-mini-info-row"><span>Accurate pricing</span><strong>Better matches</strong></div>
				</section>
			</aside>
		</div>
	</div>
</section>
