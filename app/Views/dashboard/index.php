<?php declare(strict_types=1); ?>

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Control Center</span>
				<h1 class="ss-page-title">Dashboard</h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">A quick pulse of your SkillSwap activity across services and bookings.</p>
			</div>
		</div>

		<div class="ss-grid-3">
			<div class="ss-page-card">
				<p class="ss-subtle">Public Services</p>
				<p class="ss-price-chip" style="font-size: 2.2rem; margin-top: 0.4rem;"><?php echo count($publicServices ?? []); ?></p>
			</div>

			<div class="ss-page-card">
				<p class="ss-subtle">My Seeker Bookings</p>
				<p class="ss-price-chip" style="font-size: 2.2rem; margin-top: 0.4rem;"><?php echo count($myBookings ?? []); ?></p>
			</div>

			<div class="ss-page-card">
				<p class="ss-subtle">My Provider Bookings</p>
				<p class="ss-price-chip" style="font-size: 2.2rem; margin-top: 0.4rem;"><?php echo count($providerBookings ?? []); ?></p>
			</div>
		</div>

		<div class="ss-page-card">
			<h2 style="font-size: 1.65rem; margin-bottom: 0.7rem;">Quick Actions</h2>
			<div class="ss-actions">
				<a href="/services" class="ss-btn-outline">Browse marketplace</a>
				<a href="/bookings/mine" class="ss-btn-outline">My bookings</a>
				<a href="/bookings/provider" class="ss-btn-outline">Provider schedule</a>
				<a href="/services/create" class="ss-btn-soft">Create service <span class="ss-arrow">-></span></a>
			</div>
		</div>
	</div>
</section>
