<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Provider Portal</span>
				<h1 class="ss-page-title">My Published Services</h1>
				<p class="ss-muted-block" style="margin-top: 0.45rem;">Manage offers, edit details, and review booking readiness.</p>
			</div>
			<a href="/services/create" class="ss-btn">Add New Service <span class="ss-arrow">-></span></a>
		</div>

		<?php if (($services ?? []) === []): ?>
			<div class="ss-empty-card">
				<h3>You have not created any services yet.</h3>
				<p>Publish your first service and start receiving booking requests.</p>
			</div>
		<?php else: ?>
			<div class="ss-service-list">
				<?php foreach ($services as $service): ?>
					<article class="ss-record-card">
						<span class="ss-chip"><?php echo htmlspecialchars((string) ($service['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?></span>
						<h3><?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="ss-muted-block"><?php echo htmlspecialchars((string) $service['description'], ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="ss-price-chip"><?php echo htmlspecialchars(format_currency((float) $service['price']), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="ss-record-actions">
							<a class="ss-btn-outline" href="/services/<?php echo (int) $service['id']; ?>">Open</a>
							<a class="ss-btn-outline" href="/services/<?php echo (int) $service['id']; ?>/edit">Edit</a>
							<form action="/services/<?php echo (int) $service['id']; ?>" method="post" onsubmit="return confirm('Delete this service?');">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="_method" value="DELETE">
								<button type="submit" class="ss-btn-outline" style="border-color: #d07f8f; color: #8c2f48;">Delete</button>
							</form>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
