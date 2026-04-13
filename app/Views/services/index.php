<?php declare(strict_types=1);

$allServices = is_array($services ?? null) ? $services : [];
$featuredServices = array_slice($allServices, 0, 6);
$portfolioItems = array_slice($allServices, 0, 6);

$providerMap = [];
foreach ($allServices as $service) {
	$name = trim((string) ($service['provider_name'] ?? 'Provider'));
	if ($name === '') {
		$name = 'Provider';
	}

	if (!array_key_exists($name, $providerMap)) {
		$providerMap[$name] = [
			'name' => $name,
			'role' => 'Skill Provider',
		];
	}
}

$teamMembers = array_slice(array_values($providerMap), 0, 4);

if ($teamMembers === []) {
	$teamMembers = [
		['name' => 'Aly Reyes', 'role' => 'UI Design Mentor'],
		['name' => 'Carl Santos', 'role' => 'Backend Coach'],
		['name' => 'Nina Lim', 'role' => 'Digital Marketing Expert'],
		['name' => 'Jules Dela Cruz', 'role' => 'Product Strategy Advisor'],
	];
}

$testimonialCards = [
	[
		'name' => 'Mika Rivera',
		'role' => 'Startup Founder',
		'quote' => 'I booked a design audit and then swapped back by mentoring analytics. SkillSwap made it easy to trust the process.',
	],
	[
		'name' => 'Evan Garcia',
		'role' => 'Freelance Developer',
		'quote' => 'The status flow from pending to completed is clear, and reviews helped me land repeat booking requests.',
	],
];

$serviceCount = count($allServices);
$providerCount = count($providerMap);
?>

<section class="ss-hero" id="home">
	<div class="ss-shell">
		<div class="ss-hero-grid">
			<div class="ss-hero-copy">
				<span class="ss-section-label">Smart Skill Network</span>
				<h1 class="ss-section-title">Swap Skills. Build Careers. Grow Together.</h1>
				<p class="ss-section-copy">
					SkillSwap connects seekers and providers through secure booking workflows. Publish your expertise, request services, and finish every collaboration with transparent reviews.
				</p>

				<div class="ss-hero-actions">
					<a href="#marketplace" class="ss-btn-soft">Explore Skills <span class="ss-arrow">-></span></a>
					<?php if (auth_check()): ?>
						<a href="/bookings/create" class="ss-btn-outline">Start Booking</a>
					<?php else: ?>
						<a href="/register" class="ss-btn-outline">Join SkillSwap</a>
					<?php endif; ?>

					<div class="ss-hero-help">
						<span class="ss-help-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M7.5 4.5H5.25C4.01 4.5 3 5.51 3 6.75V18.75L7.5 15.75H13.5C14.74 15.75 15.75 14.74 15.75 13.5V11.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M18.75 3.75H12.75C11.51 3.75 10.5 4.76 10.5 6V9.75C10.5 10.99 11.51 12 12.75 12H15L18.75 14.25V12C19.99 12 21 10.99 21 9.75V6C21 4.76 19.99 3.75 18.75 3.75Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>
						<div>
							<p class="ss-subtle">Need Help</p>
							<strong>(02) 555-0112</strong>
						</div>
					</div>
				</div>
			</div>

			<div class="ss-hero-visual" aria-hidden="true">
				<div class="ss-badge-disc">Trusted by local communities</div>
				<div class="ss-hero-card">
					<h3><?php echo (int) $serviceCount; ?>+ Skills</h3>
					<p>Live offers in web, design, marketing, coaching, and technical support.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="ss-section" id="marketplace">
	<div class="ss-shell">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Latest Services</span>
				<h2 class="ss-section-title ss-service-title">Explore SkillSwap's Active Marketplace</h2>
			</div>

			<?php if (auth_check()): ?>
				<a href="/services/create" class="ss-btn">Create Service <span class="ss-arrow">-></span></a>
			<?php else: ?>
				<a href="/register" class="ss-btn-outline">Join to Start Swapping</a>
			<?php endif; ?>
		</div>

		<div class="ss-services-board">
			<?php if ($featuredServices === []): ?>
				<div class="ss-empty-card">
					<h3>No services are live yet.</h3>
					<p>Be the first provider to publish a service and start your SkillSwap journey.</p>
					<a href="/services/create" class="ss-btn" style="margin-top: 1rem;">Publish First Service <span class="ss-arrow">-></span></a>
				</div>
			<?php else: ?>
				<div class="ss-grid-3">
					<?php foreach ($featuredServices as $index => $service): ?>
						<?php
						$title = (string) ($service['title'] ?? 'Skill Service');
						$iconVariant = $index % 3;
						?>
						<article class="ss-service-card ss-service-item">
							<div class="ss-service-icon" aria-hidden="true">
								<?php if ($iconVariant === 0): ?>
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8.25 7.5V5.25C8.25 4.01 9.26 3 10.5 3H13.5C14.74 3 15.75 4.01 15.75 5.25V7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
										<rect x="3" y="7.5" width="18" height="12.75" rx="2.25" stroke="currentColor" stroke-width="1.7"/>
										<path d="M3 12.75H21" stroke="currentColor" stroke-width="1.7"/>
									</svg>
								<?php elseif ($iconVariant === 1): ?>
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8.25 8.25L4.5 12L8.25 15.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M15.75 8.25L19.5 12L15.75 15.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M13.5 4.5L10.5 19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									</svg>
								<?php else: ?>
									<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M4.5 18.75H19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
										<path d="M7.5 18.75V10.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
										<path d="M12 18.75V7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
										<path d="M16.5 18.75V13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
										<path d="M7.5 7.5L10.5 4.5L13.5 7.5L18.75 3.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								<?php endif; ?>
							</div>
							<div>
								<h3><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
								<p class="ss-service-meta">By <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?></p>
							</div>

							<p class="ss-service-desc"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

							<div class="ss-service-foot">
								<span class="ss-price-chip"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
								<a href="/services/<?php echo (int) ($service['id'] ?? 0); ?>" class="ss-mini-link">Read More <span class="ss-arrow">-></span></a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="ss-section" id="about">
	<div class="ss-shell">
		<div class="ss-about-wrap">
			<div>
				<span class="ss-section-label">About Us</span>
				<h2 class="ss-section-title">We Power Practical Collaboration Through Skills.</h2>
				<p class="ss-section-copy">
					From first-time seekers to experienced providers, SkillSwap makes collaboration simple with clear policies, secure sessions, and transparent reviews.
				</p>

				<ul class="ss-about-list">
					<li>Member-to-member service publishing</li>
					<li>Structured booking status transitions</li>
					<li>Review posting after completed work</li>
					<li>CSRF, validation, and auth middleware</li>
				</ul>
			</div>

			<div class="ss-about-media" aria-hidden="true">
				<div class="ss-about-badge">
					12+<small>months of platform iteration</small>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-newsletter">
			<span class="ss-section-label" style="color: #ffffff;">Community Update</span>
			<h2>Subscribe For New Skills And Open Requests.</h2>
			<p>Get curated service highlights, provider onboarding tips, and trusted collaboration stories from the SkillSwap network.</p>

			<form class="ss-inline-form" action="#" method="post" onsubmit="return false;">
				<input type="email" name="email" placeholder="Your email here" aria-label="Email address">
				<button type="submit">Submit <span class="ss-arrow">-></span></button>
			</form>
		</div>
	</div>
</section>

<section class="ss-section">
	<div class="ss-shell ss-centered">
		<span class="ss-section-label">Latest Portfolios</span>
		<h2 class="ss-section-title ss-centered">Skill Categories In Demand</h2>

		<div class="ss-pill-row">
			<span class="ss-pill is-active">All</span>
			<span class="ss-pill">Design</span>
			<span class="ss-pill">Development</span>
			<span class="ss-pill">Marketing</span>
			<span class="ss-pill">Coaching</span>
		</div>

		<div class="ss-portfolio-grid">
			<?php if ($portfolioItems === []): ?>
				<div class="ss-empty-card" style="grid-column: 1 / -1;">Category previews appear once services are available.</div>
			<?php else: ?>
				<?php foreach ($portfolioItems as $item): ?>
					<div class="ss-portfolio-item">
						<strong><?php echo htmlspecialchars((string) ($item['title'] ?? 'Skill Project'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-team-head">
			<div>
				<span class="ss-section-label">Our Team Member</span>
				<h2 class="ss-section-title">Meet Skill Providers Building Community Value</h2>
			</div>
			<div class="ss-carousel-controls" aria-hidden="true">
				<button type="button" class="ss-circle-btn">&lt;</button>
				<button type="button" class="ss-circle-btn">&gt;</button>
			</div>
		</div>

		<div class="ss-team-grid">
			<?php foreach ($teamMembers as $member): ?>
				<article class="ss-team-card">
					<div class="ss-team-photo">
						<img src="/assets/images/default-avatar.png" alt="<?php echo htmlspecialchars((string) $member['name'], ENT_QUOTES, 'UTF-8'); ?> avatar">
					</div>
					<div class="ss-team-body">
						<h3><?php echo htmlspecialchars((string) $member['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
						<p><?php echo htmlspecialchars((string) $member['role'], ENT_QUOTES, 'UTF-8'); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="ss-section" id="workflow">
	<div class="ss-shell ss-centered">
		<span class="ss-section-label">Work Process</span>
		<h2 class="ss-section-title ss-centered">Our SkillSwap Workflow</h2>

		<div class="ss-process-grid" style="margin-top: 1.8rem;">
			<article class="ss-process-card">
				<div class="ss-process-num">01</div>
				<div class="ss-process-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4.5 18.75H19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M7.5 18.75V12.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M12 18.75V9.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M16.5 18.75V6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M6.75 8.25L11.25 5.25L14.25 7.5L18.75 3.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3>Create Offer</h3>
				<p>Providers publish structured services with clear pricing, deliverables, and expected timelines.</p>
			</article>
			<article class="ss-process-card">
				<div class="ss-process-num">02</div>
				<div class="ss-process-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="3" y="4.5" width="18" height="15" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
						<path d="M3 9H21" stroke="currentColor" stroke-width="1.8"/>
						<path d="M8.25 13.5H10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M13.5 13.5H15.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
					</svg>
				</div>
				<h3>Request Booking</h3>
				<p>Seekers submit bookings and providers approve, reject, or negotiate within policy guardrails.</p>
			</article>
			<article class="ss-process-card">
				<div class="ss-process-num">03</div>
				<div class="ss-process-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M4.5 12.75L9.75 18L19.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M12 3.75L14.06 7.92L18.66 8.59L15.33 11.84L16.12 16.41L12 14.25L7.88 16.41L8.67 11.84L5.34 8.59L9.94 7.92L12 3.75Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3>Complete + Review</h3>
				<p>Completed bookings unlock participant-only reviews to preserve trust and improve quality.</p>
			</article>
		</div>
	</div>
</section>

<section class="ss-section">
	<div class="ss-shell">
		<div class="ss-carousel-head">
			<div>
				<span class="ss-section-label">Testimonial</span>
				<h2 class="ss-section-title">What SkillSwap Members Say</h2>
			</div>
			<div class="ss-carousel-controls" aria-hidden="true">
				<button type="button" class="ss-circle-btn">&lt;</button>
				<button type="button" class="ss-circle-btn">&gt;</button>
			</div>
		</div>

		<div class="ss-testimonial-shell">
			<div class="ss-testimonial-grid">
				<?php foreach ($testimonialCards as $quote): ?>
					<article class="ss-quote-card">
						<p><?php echo htmlspecialchars((string) $quote['quote'], ENT_QUOTES, 'UTF-8'); ?></p>
						<div class="ss-quote-author">
							<img src="/assets/images/default-avatar.png" alt="<?php echo htmlspecialchars((string) $quote['name'], ENT_QUOTES, 'UTF-8'); ?> photo">
							<div>
								<strong><?php echo htmlspecialchars((string) $quote['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
								<span><?php echo htmlspecialchars((string) $quote['role'], ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="ss-dot-nav" aria-hidden="true">
				<span class="ss-dot"></span>
				<span class="ss-dot is-active"></span>
				<span class="ss-dot"></span>
			</div>

			<div class="ss-stat-row">
				<div class="ss-stat">
					<span class="ss-stat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.5 18.75H19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
							<path d="M7.5 18.75V10.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
							<path d="M12 18.75V7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
							<path d="M16.5 18.75V13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
						</svg>
					</span>
					<p>Active Skills</p>
					<strong><?php echo (int) $serviceCount; ?>+</strong>
				</div>
				<div class="ss-stat">
					<span class="ss-stat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M16.5 9.75C16.5 12.24 14.49 14.25 12 14.25C9.51 14.25 7.5 12.24 7.5 9.75C7.5 7.26 9.51 5.25 12 5.25C14.49 5.25 16.5 7.26 16.5 9.75Z" stroke="currentColor" stroke-width="1.7"/>
							<path d="M4.5 19.5C5.67 16.8 8.55 15 12 15C15.45 15 18.33 16.8 19.5 19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
						</svg>
					</span>
					<p>Providers</p>
					<strong><?php echo (int) $providerCount; ?>+</strong>
				</div>
				<div class="ss-stat">
					<span class="ss-stat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 4.5L14.2 8.96L19.12 9.67L15.56 13.14L16.4 18.03L12 15.72L7.6 18.03L8.44 13.14L4.88 9.67L9.8 8.96L12 4.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
						</svg>
					</span>
					<p>Completed Projects</p>
					<strong>500+</strong>
				</div>
				<div class="ss-stat">
					<span class="ss-stat-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M5.25 12.75L12 19.5L18.75 12.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M5.25 7.5L12 14.25L18.75 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</span>
					<p>Community Reviews</p>
					<strong>400+</strong>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="ss-section" id="stories">
	<div class="ss-shell">
		<div class="ss-blog-shell">
			<span class="ss-section-label">Latest Blog</span>
			<h2 class="ss-section-title">Recent Articles And News For Providers And Seekers</h2>

			<div class="ss-blog-grid">
				<article class="ss-blog-card">
					<div class="ss-blog-cover">
						<span>Skill Pricing Guide</span>
						<div class="ss-date-pill">28<br>APR</div>
					</div>
					<div class="ss-blog-meta">
						<span><i class="ss-meta-icon">•</i>By Admin</span>
						<span><i class="ss-meta-icon">•</i>Comments (05)</span>
					</div>
					<h3>Top 10 ways to make your first service stand out</h3>
					<a href="#" class="ss-mini-link">Read More <span class="ss-arrow">-></span></a>
				</article>

				<article class="ss-blog-card">
					<div class="ss-blog-cover">
						<span>Workflow Tips</span>
						<div class="ss-date-pill">15<br>JUN</div>
					</div>
					<div class="ss-blog-meta">
						<span><i class="ss-meta-icon">•</i>By Admin</span>
						<span><i class="ss-meta-icon">•</i>Comments (05)</span>
					</div>
					<h3>How seekers can write better booking requests</h3>
					<a href="#" class="ss-mini-link">Read More <span class="ss-arrow">-></span></a>
				</article>

				<article class="ss-blog-card">
					<div class="ss-blog-cover">
						<span>Provider Growth</span>
						<div class="ss-date-pill">29<br>MAY</div>
					</div>
					<div class="ss-blog-meta">
						<span><i class="ss-meta-icon">•</i>By Admin</span>
						<span><i class="ss-meta-icon">•</i>Comments (05)</span>
					</div>
					<h3>Building trust with profile quality and reviews</h3>
					<a href="#" class="ss-mini-link">Read More <span class="ss-arrow">-></span></a>
				</article>
			</div>
		</div>
	</div>
</section>
