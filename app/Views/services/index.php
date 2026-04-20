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

	$avatar = profile_image_url((string) ($service['provider_profile_picture'] ?? ''));

	if (!array_key_exists($name, $providerMap)) {
		$providerMap[$name] = [
			'name' => $name,
			'role' => 'Skill Provider',
			'avatar' => $avatar,
		];
	}
}

$teamMembers = array_slice(array_values($providerMap), 0, 4);

if ($teamMembers === []) {
	$teamMembers = [
		['name' => 'Aly Reyes', 'role' => 'UI Design Mentor', 'avatar' => '/assets/images/default-avatar.png'],
		['name' => 'Carl Santos', 'role' => 'Backend Coach', 'avatar' => '/assets/images/default-avatar.png'],
		['name' => 'Nina Lim', 'role' => 'Digital Marketing Expert', 'avatar' => '/assets/images/default-avatar.png'],
		['name' => 'Jules Dela Cruz', 'role' => 'Product Strategy Advisor', 'avatar' => '/assets/images/default-avatar.png'],
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
$defaultAvatarImage = profile_image_url('');
?>

<section class="relative overflow-hidden bg-surface-page py-16" id="home">
	<div aria-hidden="true" class="pointer-events-none absolute -left-24 top-20 h-72 w-72 rounded-full border border-brand-200"></div>
	<div aria-hidden="true" class="pointer-events-none absolute right-8 top-8 h-80 w-80 rounded-full border border-brand-200"></div>

	<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
		<div class="grid items-center gap-8 lg:grid-cols-[1fr_1fr]">
			<div>
				<span class="inline-flex items-center gap-5 text-sm font-extrabold uppercase tracking-[0.2em] text-brand-700">
					<span class="h-px w-10 bg-brand-300"></span>
					Smart Skill Network
					<span class="h-px w-10 bg-brand-300"></span>
				</span>
				<h1 class="mt-5 max-w-[12ch] text-6xl font-heading font-extrabold leading-[1.02] tracking-tight text-neutral-950">Swap Skills. Build Careers. Grow Together.</h1>
				<p class="mt-6 max-w-[58ch] text-[1.08rem] leading-9 text-neutral-700">
					SkillSwap connects seekers and providers through secure booking workflows. Publish your expertise, request services, and finish every collaboration with transparent reviews.
				</p>

				<div class="mt-8 flex flex-wrap items-center gap-3">
					<a href="#marketplace" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand-500 px-7 py-3.5 text-xl font-extrabold text-white shadow-brand transition hover:bg-brand-600">Explore Skills <span aria-hidden="true">-></span></a>
					<?php if (auth_check()): ?>
						<a href="/services" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-7 py-3.5 text-xl font-extrabold text-neutral-900 transition hover:border-brand-500 hover:text-brand-700">Start Booking</a>
					<?php else: ?>
						<a href="/register" class="inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-7 py-3.5 text-xl font-extrabold text-neutral-900 transition hover:border-brand-500 hover:text-brand-700">Join SkillSwap</a>
					<?php endif; ?>

					<div class="inline-flex items-center gap-3 rounded-full border border-neutral-200 bg-white px-5 py-3.5 shadow-soft">
						<span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-warning-400 text-neutral-900" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5">
								<path d="M7.5 4.5H5.25C4.01 4.5 3 5.51 3 6.75V18.75L7.5 15.75H13.5C14.74 15.75 15.75 14.74 15.75 13.5V11.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M18.75 3.75H12.75C11.51 3.75 10.5 4.76 10.5 6V9.75C10.5 10.99 11.51 12 12.75 12H15L18.75 14.25V12C19.99 12 21 10.99 21 9.75V6C21 4.76 19.99 3.75 18.75 3.75Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>
						<div>
							<p class="text-sm font-bold uppercase tracking-wide text-neutral-600">Need Help</p>
							<strong class="text-2xl font-extrabold leading-tight text-neutral-900">(02) 555-0112</strong>
						</div>
					</div>
				</div>
			</div>

			<div class="relative min-h-[32rem] overflow-hidden rounded-[2.25rem] bg-gradient-to-br from-brand-900 via-brand-700 to-brandAccent-tintSoft p-6 shadow-card">
				<div aria-hidden="true" class="absolute -left-12 top-7 inline-flex h-40 w-40 items-center justify-center rounded-full bg-white px-5 text-center text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">Trusted by local communities</div>
				<div aria-hidden="true" class="absolute -right-8 -top-8 h-72 w-72 rounded-full border border-overlay-white35"></div>

				<div class="absolute bottom-8 left-6 max-w-md rounded-3xl border border-overlay-white35 bg-white/90 px-6 py-5 shadow-soft">
					<h3 class="text-5xl font-heading font-extrabold leading-tight text-neutral-950"><?php echo (int) $serviceCount; ?>+ Skills</h3>
					<p class="mt-2 text-lg leading-8 text-neutral-700">Live offers in web, design, marketing, coaching, and technical support.</p>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-14 md:py-16" id="marketplace">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Latest Services
				</span>
				<h2 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Explore SkillSwap's Active Marketplace</h2>
			</div>

			<?php if (auth_check()): ?>
				<a href="/services/create" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Create Service <span aria-hidden="true">-></span></a>
			<?php else: ?>
				<a href="/register" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Join to Start Swapping</a>
			<?php endif; ?>
		</div>

		<?php if ($featuredServices === []): ?>
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">No services are live yet.</h3>
				<p class="mt-2 text-sm text-neutral-600">Be the first provider to publish a service and start your SkillSwap journey.</p>
				<a href="/services/create" class="mt-5 inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Publish First Service <span aria-hidden="true">-></span></a>
			</div>
		<?php else: ?>
			<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
				<?php foreach ($featuredServices as $index => $service): ?>
					<?php
					$title = (string) ($service['title'] ?? 'Skill Service');
					$iconVariant = $index % 3;
					$providerImage = profile_image_url((string) ($service['provider_profile_picture'] ?? ''));
					$serviceLink = '/services/' . (int) ($service['id'] ?? 0);
					$surfaceClass = $iconVariant === 0 ? 'bg-surface-rose' : ($iconVariant === 1 ? 'bg-surface-page' : 'bg-surface-warm');
					?>
					<article class="group grid min-h-[17rem] gap-4 rounded-3xl border border-neutral-300 p-6 shadow-soft transition hover:-translate-y-1 hover:shadow-brand <?php echo htmlspecialchars($surfaceClass, ENT_QUOTES, 'UTF-8'); ?>" data-card-link="<?php echo htmlspecialchars($serviceLink, ENT_QUOTES, 'UTF-8'); ?>" role="link" tabindex="0" aria-label="Open <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?> details">
						<div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-brand-200 bg-white text-brand-700" aria-hidden="true">
							<?php if ($iconVariant === 0): ?>
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
									<path d="M8.25 7.5V5.25C8.25 4.01 9.26 3 10.5 3H13.5C14.74 3 15.75 4.01 15.75 5.25V7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									<rect x="3" y="7.5" width="18" height="12.75" rx="2.25" stroke="currentColor" stroke-width="1.7"/>
									<path d="M3 12.75H21" stroke="currentColor" stroke-width="1.7"/>
								</svg>
							<?php elseif ($iconVariant === 1): ?>
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
									<path d="M8.25 8.25L4.5 12L8.25 15.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M15.75 8.25L19.5 12L15.75 15.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M13.5 4.5L10.5 19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
								</svg>
							<?php else: ?>
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
									<path d="M4.5 18.75H19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									<path d="M7.5 18.75V10.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									<path d="M12 18.75V7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									<path d="M16.5 18.75V13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									<path d="M7.5 7.5L10.5 4.5L13.5 7.5L18.75 3.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							<?php endif; ?>
						</div>
						<div>
							<h3 class="text-[1.45rem] font-extrabold leading-tight text-neutral-950"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
							<div class="mt-2 flex items-center gap-2">
								<img src="<?php echo htmlspecialchars($providerImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?> profile picture" class="h-9 w-9 rounded-full border border-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
								<p class="text-sm font-semibold text-neutral-700">By <?php echo htmlspecialchars((string) ($service['provider_name'] ?? 'Provider'), ENT_QUOTES, 'UTF-8'); ?></p>
							</div>
						</div>

						<p class="text-sm leading-7 text-neutral-700"><?php echo htmlspecialchars((string) ($service['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="mt-auto flex items-center justify-between gap-3">
							<span class="text-2xl font-heading font-extrabold text-brand-700"><?php echo htmlspecialchars(format_currency((float) ($service['price'] ?? 0)), ENT_QUOTES, 'UTF-8'); ?></span>
							<a href="<?php echo htmlspecialchars($serviceLink, ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700" data-no-card-nav>Read More <span aria-hidden="true">-></span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="py-14 md:py-16" id="about">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
		<div class="grid items-center gap-8 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-10 lg:grid-cols-[1.15fr,0.85fr]">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					About Us
				</span>
				<h2 class="mt-3 max-w-[16ch] text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">We Power Practical Collaboration Through Skills.</h2>
				<p class="mt-4 max-w-[64ch] text-sm leading-8 text-neutral-700">From first-time seekers to experienced providers, SkillSwap makes collaboration simple with clear policies, secure sessions, and transparent reviews.</p>
				<ul class="mt-5 grid gap-2 text-sm text-neutral-700 md:grid-cols-2">
					<li>Member-to-member service publishing</li>
					<li>Structured booking status transitions</li>
					<li>Review posting after completed work</li>
					<li>CSRF, validation, and auth middleware</li>
				</ul>
			</div>

			<div class="relative min-h-[15rem] overflow-hidden rounded-3xl bg-gradient-to-br from-brand-900 via-brand-700 to-brandAccent-tintSoft p-6 text-white">
				<div aria-hidden="true" class="absolute -right-10 -top-10 h-40 w-40 rounded-full border border-overlay-white35"></div>
				<div class="absolute bottom-6 left-6 rounded-2xl border border-overlay-white35 bg-overlay-white18 px-5 py-4 text-center">
					<strong class="block text-4xl font-heading font-extrabold">12+</strong>
					<span class="text-xs font-bold uppercase tracking-[0.12em]">months of platform iteration</span>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
		<div class="rounded-3xl bg-gradient-to-r from-brand-900 via-brand-700 to-brandAccent-tintSoft p-7 text-white shadow-card md:p-10">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-100">
				<span class="h-px w-8 bg-overlay-white35"></span>
				Community Update
			</span>
			<h2 class="mt-3 text-3xl font-heading font-extrabold">Subscribe For New Skills And Open Requests.</h2>
			<p class="mt-3 max-w-[60ch] text-sm leading-8 text-brand-100">Get curated service highlights, provider onboarding tips, and trusted collaboration stories from the SkillSwap network.</p>

			<form class="mt-5 flex flex-col gap-3 sm:flex-row" action="#" method="post" onsubmit="return false;">
				<input type="email" name="email" placeholder="Your email here" aria-label="Email address" class="h-12 w-full rounded-full border border-overlay-white35 bg-overlay-white18 px-5 text-sm font-semibold text-white placeholder:text-brand-100 focus:outline-none focus:ring-2 focus:ring-white sm:max-w-sm">
				<button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-white px-6 text-sm font-extrabold text-brand-700 transition hover:bg-brand-100">Submit <span aria-hidden="true">-></span></button>
			</form>
		</div>
	</div>
</section>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-6 text-center">
		<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
			<span class="h-px w-8 bg-brand-300"></span>
			Latest Portfolios
		</span>
		<h2 class="mx-auto max-w-[16ch] text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Skill Categories In Demand</h2>

		<div class="flex flex-wrap justify-center gap-2">
			<span class="rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-brand-700">All</span>
			<span class="rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-neutral-700">Design</span>
			<span class="rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-neutral-700">Development</span>
			<span class="rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-neutral-700">Marketing</span>
			<span class="rounded-full border border-neutral-300 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-neutral-700">Coaching</span>
		</div>

		<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
			<?php if ($portfolioItems === []): ?>
				<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-sm text-neutral-600 md:col-span-2 xl:col-span-3">Category previews appear once services are available.</div>
			<?php else: ?>
				<?php foreach ($portfolioItems as $item): ?>
					<div class="rounded-3xl border border-neutral-200 bg-white p-6 text-left shadow-soft">
						<strong class="text-lg font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars((string) ($item['title'] ?? 'Skill Project'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-6">
		<div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Our Team Member
				</span>
				<h2 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Meet Skill Providers Building Community Value</h2>
			</div>
			<div class="hidden items-center gap-2 md:flex" aria-hidden="true">
				<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-xl font-black text-neutral-700">&lt;</button>
				<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-xl font-black text-neutral-700">&gt;</button>
			</div>
		</div>

		<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
			<?php foreach ($teamMembers as $member): ?>
				<article class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
					<div class="overflow-hidden rounded-2xl border border-neutral-200 bg-surface-page">
						<img src="<?php echo htmlspecialchars(profile_image_url((string) ($member['avatar'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $member['name'], ENT_QUOTES, 'UTF-8'); ?> avatar" class="h-48 w-full object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					<div class="mt-4">
						<h3 class="text-xl font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars((string) $member['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="mt-1 text-sm text-neutral-600"><?php echo htmlspecialchars((string) $member['role'], ENT_QUOTES, 'UTF-8'); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="py-14 md:py-16" id="workflow">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-6 text-center">
		<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
			<span class="h-px w-8 bg-brand-300"></span>
			Work Process
		</span>
		<h2 class="mx-auto max-w-[16ch] text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Our SkillSwap Workflow</h2>

		<div class="grid gap-4 pt-2 md:grid-cols-3">
			<article class="rounded-3xl border border-neutral-200 bg-white p-6 text-left shadow-soft">
				<div class="mb-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">01</div>
				<div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-brand-200 bg-surface-page text-brand-700" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
						<path d="M4.5 18.75H19.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M7.5 18.75V12.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M12 18.75V9.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M16.5 18.75V6.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M6.75 8.25L11.25 5.25L14.25 7.5L18.75 3.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">Create Offer</h3>
				<p class="mt-2 text-sm leading-7 text-neutral-700">Providers publish structured services with clear pricing, deliverables, and expected timelines.</p>
			</article>

			<article class="rounded-3xl border border-neutral-200 bg-white p-6 text-left shadow-soft">
				<div class="mb-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">02</div>
				<div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-brand-200 bg-surface-page text-brand-700" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
						<rect x="3" y="4.5" width="18" height="15" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
						<path d="M3 9H21" stroke="currentColor" stroke-width="1.8"/>
						<path d="M8.25 13.5H10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M13.5 13.5H15.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
					</svg>
				</div>
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">Request Booking</h3>
				<p class="mt-2 text-sm leading-7 text-neutral-700">Seekers submit bookings and providers approve, reject, or negotiate within policy guardrails.</p>
			</article>

			<article class="rounded-3xl border border-neutral-200 bg-white p-6 text-left shadow-soft">
				<div class="mb-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">03</div>
				<div class="mb-4 inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-brand-200 bg-surface-page text-brand-700" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6">
						<path d="M4.5 12.75L9.75 18L19.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M12 3.75L14.06 7.92L18.66 8.59L15.33 11.84L16.12 16.41L12 14.25L7.88 16.41L8.67 11.84L5.34 8.59L9.94 7.92L12 3.75Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
					</svg>
				</div>
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">Complete + Review</h3>
				<p class="mt-2 text-sm leading-7 text-neutral-700">Completed bookings unlock participant-only reviews to preserve trust and improve quality.</p>
			</article>
		</div>
	</div>
</section>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Testimonial
				</span>
				<h2 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">What SkillSwap Members Say</h2>
			</div>
			<div class="hidden items-center gap-2 md:flex" aria-hidden="true">
				<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-xl font-black text-neutral-700">&lt;</button>
				<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-xl font-black text-neutral-700">&gt;</button>
			</div>
		</div>

		<div class="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<div class="grid gap-4 lg:grid-cols-2">
				<?php foreach ($testimonialCards as $quote): ?>
					<article class="rounded-3xl border border-neutral-200 bg-surface-page p-5">
						<p class="text-sm leading-8 text-neutral-700"><?php echo htmlspecialchars((string) $quote['quote'], ENT_QUOTES, 'UTF-8'); ?></p>
						<div class="mt-4 flex items-center gap-3">
							<img src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars((string) $quote['name'], ENT_QUOTES, 'UTF-8'); ?> photo" class="h-10 w-10 rounded-full border border-neutral-200 object-cover" data-fallback-src="<?php echo htmlspecialchars($defaultAvatarImage, ENT_QUOTES, 'UTF-8'); ?>">
							<div>
								<strong class="block text-sm font-extrabold text-neutral-900"><?php echo htmlspecialchars((string) $quote['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
								<span class="text-xs font-semibold uppercase tracking-wide text-neutral-500"><?php echo htmlspecialchars((string) $quote['role'], ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="flex justify-center gap-2" aria-hidden="true">
				<span class="h-2 w-2 rounded-full bg-neutral-300"></span>
				<span class="h-2 w-6 rounded-full bg-brand-600"></span>
				<span class="h-2 w-2 rounded-full bg-neutral-300"></span>
			</div>

			<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
				<div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
					<p class="text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-500">Active Skills</p>
					<strong class="mt-1 block text-2xl font-heading font-extrabold text-neutral-950"><?php echo (int) $serviceCount; ?>+</strong>
				</div>
				<div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
					<p class="text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-500">Providers</p>
					<strong class="mt-1 block text-2xl font-heading font-extrabold text-neutral-950"><?php echo (int) $providerCount; ?>+</strong>
				</div>
				<div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
					<p class="text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-500">Completed Projects</p>
					<strong class="mt-1 block text-2xl font-heading font-extrabold text-neutral-950">500+</strong>
				</div>
				<div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
					<p class="text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-500">Community Reviews</p>
					<strong class="mt-1 block text-2xl font-heading font-extrabold text-neutral-950">400+</strong>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="py-14 md:py-16" id="stories">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-6">
		<div>
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Latest Blog
			</span>
			<h2 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Recent Articles And News For Providers And Seekers</h2>
		</div>

		<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
			<article class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
				<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-800 to-brand-600 p-4 text-white">
					<span class="text-xs font-extrabold uppercase tracking-[0.12em]">Skill Pricing Guide</span>
					<div class="absolute right-4 top-4 rounded-xl bg-white/20 px-2 py-1 text-center text-xs font-extrabold leading-4">28<br>APR</div>
				</div>
				<div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wide text-neutral-500">
					<span>By Admin</span>
					<span>Comments (05)</span>
				</div>
				<h3 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Top 10 ways to make your first service stand out</h3>
				<a href="#" class="mt-4 inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700">Read More <span aria-hidden="true">-></span></a>
			</article>

			<article class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
				<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-700 to-brandAccent-tintSoft p-4 text-white">
					<span class="text-xs font-extrabold uppercase tracking-[0.12em]">Workflow Tips</span>
					<div class="absolute right-4 top-4 rounded-xl bg-white/20 px-2 py-1 text-center text-xs font-extrabold leading-4">15<br>JUN</div>
				</div>
				<div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wide text-neutral-500">
					<span>By Admin</span>
					<span>Comments (05)</span>
				</div>
				<h3 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">How seekers can write better booking requests</h3>
				<a href="#" class="mt-4 inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700">Read More <span aria-hidden="true">-></span></a>
			</article>

			<article class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
				<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-brand-900 to-brand-700 p-4 text-white">
					<span class="text-xs font-extrabold uppercase tracking-[0.12em]">Provider Growth</span>
					<div class="absolute right-4 top-4 rounded-xl bg-white/20 px-2 py-1 text-center text-xs font-extrabold leading-4">29<br>MAY</div>
				</div>
				<div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-wide text-neutral-500">
					<span>By Admin</span>
					<span>Comments (05)</span>
				</div>
				<h3 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Building trust with profile quality and reviews</h3>
				<a href="#" class="mt-4 inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700">Read More <span aria-hidden="true">-></span></a>
			</article>
		</div>
	</div>
</section>
