<?php declare(strict_types=1); ?>

<?php
$actor = auth_user();
$actorRole = is_array($actor) ? strtolower((string) ($actor['role'] ?? 'member')) : 'guest';
$actorId = is_array($actor) ? (int) ($actor['id'] ?? 0) : 0;
$canSeekSkill = $actorRole !== 'provider';
$canOfferSkill = $actorRole !== 'seeker';
?>

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Marketplace</span>
				<h1 class="ss-page-title">Find Skills To Learn Or Swap</h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">Browse active offers, book what you need, or publish your own service.</p>
			</div>
			<div class="ss-actions">
				<?php if ($canSeekSkill): ?>
					<a href="/bookings/mine" class="ss-btn-outline">My bookings</a>
				<?php endif; ?>
				<?php if ($canOfferSkill): ?>
					<a href="/services/create" class="ss-btn-soft">Offer skill <span class="ss-arrow">-></span></a>
				<?php endif; ?>
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
					<?php
					$providerImage = profile_image_url((string) ($service['provider_profile_picture'] ?? ''));
					$isOwnService = $actorId > 0 && $actorId === (int) ($service['user_id'] ?? 0);
					$serviceLink = '/services/' . (int) ($service['id'] ?? 0);
					?>
					<article class="ss-service-card" data-card-link="<?php echo htmlspecialchars($serviceLink, ENT_QUOTES, 'UTF-8'); ?>" role="link" tabindex="0" aria-label="Open <?php echo htmlspecialchars((string) ($service['title'] ?? 'service'), ENT_QUOTES, 'UTF-8'); ?> details">
						<div>
							<h3><?php echo htmlspecialchars((string) ($service['title'] ?? 'Skill Service'), ENT_QUOTES, 'UTF-8'); ?></h3>
							<div class="ss-provider-row" style="margin-top: 0.35rem;">
								<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="ss-provider-avatar">
								<p class="ss-service-meta">By <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						</div>

						<p class="ss-service-desc"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="ss-service-foot">
							<span class="ss-price-chip"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
							<div class="ss-actions" style="gap: 0.45rem;">
								<a href="<?php echo htmlspecialchars($serviceLink, ENT_QUOTES, 'UTF-8'); ?>" class="ss-mini-link">View</a>
								<?php if (!$isOwnService): ?>
									<a href="/bookings/create?service_id=<?php echo (int) ($service['id'] ?? 0); ?>" class="ss-mini-link">Book</a>
								<?php else: ?>
									<span class="ss-subtle">Your service</span>
								<?php endif; ?>
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
				<?php if ($canOfferSkill): ?>
					<a href="/services/mine" class="ss-btn-outline">My services</a>
				<?php endif; ?>
				<?php if ($canSeekSkill): ?>
					<a href="/bookings/mine" class="ss-btn-outline">My bookings</a>
				<?php endif; ?>
				<?php if ($canOfferSkill): ?>
					<a href="/bookings/provider" class="ss-btn-outline">Provider schedule</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
