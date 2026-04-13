<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<section class="ss-section">
		<div class="ss-shell">
			<div class="ss-empty-card">
				<h2>Service not found.</h2>
				<p>The requested service could not be loaded for editing.</p>
				<a href="/services/mine" class="ss-btn" style="margin-top: 1rem;">Back to my services <span class="ss-arrow">-></span></a>
			</div>
		</div>
	</section>
<?php else: ?>
	<section class="ss-section">
		<div class="ss-shell">
			<div class="ss-page-head">
				<div>
					<span class="ss-section-label">Provider Portal</span>
					<h1 class="ss-page-title">Edit Service</h1>
					<p class="ss-muted-block" style="margin-top: 0.4rem;">Update pricing, description, and status while keeping bookings consistent.</p>
				</div>
			</div>

			<div class="ss-split-shell">
				<form action="/services/<?php echo (int) $service['id']; ?>" method="post" class="ss-form-shell ss-split-main">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="_method" value="PATCH">

					<div class="ss-field">
						<label for="title">Service title</label>
						<input id="title" name="title" type="text" required value="<?php echo htmlspecialchars((string) old_input('title', (string) $service['title']), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
					</div>

					<div class="ss-field">
						<label for="description">Description</label>
						<textarea id="description" name="description" rows="6" required class="ss-textarea"><?php echo htmlspecialchars((string) old_input('description', (string) $service['description']), ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>

					<div class="ss-field">
						<label for="price">Price</label>
						<input id="price" name="price" type="number" min="1" step="0.01" required value="<?php echo htmlspecialchars((string) old_input('price', (string) $service['price']), ENT_QUOTES, 'UTF-8'); ?>" class="ss-input">
					</div>

					<div class="ss-field">
						<label for="status">Status</label>
						<?php $status = (string) old_input('status', (string) $service['status']); ?>
						<select id="status" name="status" class="ss-select">
							<option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
							<option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
						</select>
					</div>

					<div class="ss-actions">
						<button type="submit" class="ss-btn-soft">Update Service <span class="ss-arrow">-></span></button>
						<a href="/services/<?php echo (int) $service['id']; ?>" class="ss-btn-outline">Cancel</a>
					</div>
				</form>

				<aside class="ss-split-aside">
					<section class="ss-accent-panel">
						<span class="ss-accent-tag">Edit Strategy</span>
						<h2>Adjust details without confusing active clients.</h2>
						<p>Keep your listing aligned with what current and future bookings can realistically expect.</p>
						<ul class="ss-accent-list">
							<li>Reflect any scope change in the description.</li>
							<li>Avoid frequent price changes in short intervals.</li>
							<li>Set inactive if you need time to update workflow.</li>
						</ul>
					</section>

					<section class="ss-page-card ss-mini-info-card">
						<h3 style="font-size: 1rem;">Service Controls</h3>
						<div class="ss-mini-info-row"><span>Current service ID</span><strong>#<?php echo (int) $service['id']; ?></strong></div>
						<div class="ss-mini-info-row"><span>Status toggles</span><strong>Live now</strong></div>
					</section>

					<section class="ss-page-card ss-mini-info-card">
						<h3 style="font-size: 1rem; color: #8c2f48;">Danger Zone</h3>
						<p class="ss-subtle">Deleting a service is permanent and may affect past references.</p>
						<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service? This action cannot be undone.');" style="margin-top: 0.5rem;">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="_method" value="DELETE">
							<button type="submit" class="ss-btn-outline" style="border-color: #d07f8f; color: #8c2f48;">Delete Service</button>
						</form>
					</section>
				</aside>
			</div>
		</div>
	</section>
<?php endif; ?>
