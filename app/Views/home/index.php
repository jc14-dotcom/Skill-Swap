<?php declare(strict_types=1); ?>

<?php
$userName = htmlspecialchars((string) ($user['name'] ?? 'Member'), ENT_QUOTES, 'UTF-8');
$roleLabel = strtoupper(htmlspecialchars((string) ($role ?? 'seeker'), ENT_QUOTES, 'UTF-8'));
?>

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Member Home</span>
				<h1 class="ss-page-title">Welcome, <?php echo $userName; ?></h1>
				<p class="ss-muted-block" style="margin-top: 0.5rem;">You are logged in as <?php echo $roleLabel; ?>. In SkillSwap, every member can seek new skills and also offer their own.</p>
			</div>
			<div class="ss-actions">
				<a href="/services" class="ss-btn-outline">Open marketplace</a>
				<a href="/bookings/create" class="ss-btn-soft">Seek a skill <span class="ss-arrow">-></span></a>
				<a href="/services/create" class="ss-btn-outline">Offer a skill</a>
			</div>
		</div>

		<div class="ss-grid-3">
			<div class="ss-page-card">
				<p class="ss-subtle">Public Services</p>
				<p class="ss-price-chip" style="font-size: 2.2rem; margin-top: 0.4rem;"><?php echo (int) ($publicServiceCount ?? 0); ?></p>
			</div>

			<div class="ss-page-card">
				<p class="ss-subtle">My Seeker Bookings</p>
				<p class="ss-price-chip" style="font-size: 2.2rem; margin-top: 0.4rem;"><?php echo (int) ($myBookingCount ?? 0); ?></p>
			</div>

			<div class="ss-page-card">
				<p class="ss-subtle">Incoming Provider Bookings</p>
				<p class="ss-price-chip" style="font-size: 2.2rem; margin-top: 0.4rem;"><?php echo (int) ($providerBookingCount ?? 0); ?></p>
			</div>
		</div>

		<div class="ss-page-card">
			<h2 style="font-size: 1.65rem; margin-bottom: 0.7rem;">Quick Actions</h2>
			<div class="ss-actions">
				<a href="/bookings/create" class="ss-btn-outline">Seek skill</a>
				<a href="/services/create" class="ss-btn-outline">Offer skill</a>
				<a href="/services/mine" class="ss-btn-outline">My services (<?php echo (int) ($myServiceCount ?? 0); ?>)</a>
				<a href="/bookings/mine" class="ss-btn-outline">My bookings</a>
				<a href="/bookings/provider" class="ss-btn-outline">Provider queue</a>
				<a href="/dashboard" class="ss-btn-outline">Open dashboard</a>
				<a href="/services/create" class="ss-btn-soft">Create service <span class="ss-arrow">-></span></a>
			</div>
		</div>
	</div>
</section>
