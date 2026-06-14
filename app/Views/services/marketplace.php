<?php declare(strict_types=1); ?>

<?php
$actor = auth_user();
$actorRole = is_array($actor) ? strtolower((string) ($actor['role'] ?? 'member')) : 'guest';
$actorId = is_array($actor) ? (int) ($actor['id'] ?? 0) : 0;
$canSeekSkill = $actorRole !== 'provider';
$canOfferSkill = $actorRole !== 'seeker';
$searchQuery = trim((string) ($searchQuery ?? ''));
$isSearching = $searchQuery !== '';

$outlineButtonClasses = 'inline-flex items-center justify-center rounded-full border border-neutral-400 bg-white px-5 py-3 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700';
$primaryButtonClasses = 'inline-flex items-center justify-center gap-2 rounded-full bg-brand-500 px-5 py-3 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600';
$miniLinkClasses = 'inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700';
?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto grid w-[min(1320px,calc(100%-2rem))] gap-6">
		<div class="flex flex-wrap items-start justify-between gap-4">
			<div>
				<span class="mb-3 inline-flex text-xs font-extrabold uppercase tracking-[0.2em] text-brand-500">Skill Exchange</span>
				<h1 class="max-w-[16ch] text-4xl font-extrabold leading-tight text-neutral-950 md:text-5xl">Find Skills To Learn Or Swap</h1>
				<p class="mt-2 text-base text-neutral-700">Browse active offers, book what you need, or publish your own service.</p>
			</div>
			<div class="flex flex-wrap items-center gap-3">
				<?php if ($canSeekSkill): ?>
					<a href="/bookings/mine" class="<?php echo e($outlineButtonClasses); ?>">My bookings</a>
				<?php endif; ?>
				<?php if ($canOfferSkill): ?>
					<a href="/services/create" class="<?php echo e($primaryButtonClasses); ?>">Offer skill <span aria-hidden="true">-></span></a>
				<?php endif; ?>
			</div>
		</div>

		<form
			action="/services"
			method="get"
			class="rounded-3xl border border-neutral-300 bg-surface-card p-4 shadow-soft"
			role="search"
			aria-label="Search skills"
			data-live-search-form
			data-live-search-endpoint="/services?format=json"
			data-search-max-length="120"
		>
			<label for="market-search-input" class="mb-2 block text-sm font-extrabold tracking-wide text-brand-800">Search by skill title, type, or provider</label>
			<div class="block">
				<div class="flex min-h-12 items-center gap-3 rounded-2xl border border-neutral-300 bg-neutral-50 px-4 transition focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brandSoft">
					<span class="h-5 w-5 flex-shrink-0 text-brand-600" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="11" cy="11" r="6.6" stroke="currentColor" stroke-width="1.9"/>
							<path d="M16 16L20.5 20.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
						</svg>
					</span>
					<input
						id="market-search-input"
						type="search"
						name="q"
						value="<?php echo e($searchQuery); ?>"
						class="h-12 w-full min-w-0 border-0 bg-transparent p-0 text-lg text-neutral-900 placeholder:text-neutral-500 focus:outline-none"
						data-live-search-input
						data-live-search-min-length="2"
						maxlength="120"
						placeholder="Example: graphic design, coding, tutoring, language"
						autocomplete="off"
					>
				</div>
			</div>
			<p class="mt-2 text-sm font-semibold text-neutral-700" data-live-search-status aria-live="polite" <?php echo $isSearching ? '' : 'hidden'; ?>>
				<?php if ($isSearching): ?>
					Showing results for "<?php echo e($searchQuery); ?>".
				<?php endif; ?>
			</p>
			<span class="hidden text-neutral-600 text-neutral-700 text-info-700 text-danger-700"></span>
		</form>

		<div class="grid gap-4" data-live-search-results-wrap>
			<div class="grid grid-cols-1 gap-6 xl:grid-cols-3" data-live-search-results <?php echo ($services ?? []) === [] ? 'hidden' : ''; ?>>
				<?php foreach ($services as $index => $service): ?>
					<?php
					$providerName = trim((string) ($service['provider_name'] ?? 'Provider'));
					if ($providerName === '') {
						$providerName = 'Provider';
					}
					$boostExpiresAt = (string) ($service['boost_expires_at'] ?? '');
					$boostActive = ((int) ($service['is_boosted'] ?? 0) === 1)
						&& ($boostExpiresAt !== '' && strtotime($boostExpiresAt) > time());
					$providerFallback = initials_avatar_data_uri($providerName);
					$providerImage = profile_image_url((string) ($service['provider_profile_picture'] ?? ''));
					if ($providerImage === '') {
						$providerImage = $providerFallback;
					}
					$isOwnService = $actorId > 0 && $actorId === (int) ($service['user_id'] ?? 0);
					$serviceLink = '/services/' . (int) ($service['id'] ?? 0);
					?>
					<article class="group grid min-h-[17rem] gap-4 rounded-3xl border border-neutral-300 bg-white p-6 shadow-soft transition hover:-translate-y-1 hover:border-brand-200 hover:bg-surface-rose hover:shadow-brand" data-card-link="<?php echo e($serviceLink); ?>" role="link" tabindex="0" aria-label="Open <?php echo e($service['title'] ?? 'service'); ?> details">
						<div>
							<?php if ($boostActive): ?>
								<span class="mb-2 inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-amber-800">Boosted</span>
							<?php endif; ?>
							<h3 class="text-[1.45rem] font-extrabold leading-tight text-neutral-950"><?php echo e($service['title'] ?? 'Skill Service'); ?></h3>
							<div class="mt-1.5 flex items-center gap-3">
								<img src="<?php echo e($providerImage); ?>" alt="<?php echo e($providerName); ?> profile picture" class="h-9 w-9 rounded-full border-2 border-brand-200 bg-neutral-200 object-cover" data-fallback-src="<?php echo e($providerFallback); ?>" onerror="this.onerror=null;this.src=this.getAttribute('data-fallback-src');">
								<p class="text-sm font-bold text-neutral-700">By <?php echo e($providerName); ?></p>
							</div>
						</div>

						<p class="text-sm leading-7 text-neutral-700"><?php echo e($service['description'] ?? ''); ?></p>

						<div class="mt-auto flex items-center justify-between gap-3">
							<span class="rounded-full bg-white/90 px-3 py-1 text-sm font-extrabold text-brand-700"><?php echo e(format_currency((float) ($service['price'] ?? 0))); ?></span>
							<div class="flex items-center gap-3">
								<a href="<?php echo e($serviceLink); ?>" class="<?php echo e($miniLinkClasses); ?>">View</a>
								<?php if (!$isOwnService): ?>
									<a href="/bookings/create?service_id=<?php echo (int) ($service['id'] ?? 0); ?>" class="<?php echo e($miniLinkClasses); ?>">Book</a>
								<?php else: ?>
									<span class="text-sm font-semibold text-neutral-600">Your service</span>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>

			<div class="rounded-3xl border border-neutral-300 bg-surface-card px-6 py-12 text-center shadow-soft" data-live-search-empty <?php echo ($services ?? []) !== [] ? 'hidden' : ''; ?>>
				<h3 class="text-2xl font-extrabold text-neutral-950" data-live-search-empty-title>
					<?php if ($isSearching): ?>
						No skills found for "<?php echo e($searchQuery); ?>".
					<?php else: ?>
						No active services yet.
					<?php endif; ?>
				</h3>
				<p class="mt-2 text-base text-neutral-700" data-live-search-empty-copy>
					<?php if ($isSearching): ?>
						Try another keyword like design, editing, coding, tutoring, or language.
					<?php else: ?>
						Be the first to offer a skill and start swapping with the community.
					<?php endif; ?>
				</p>
				<?php if ($canOfferSkill): ?>
					<div class="mt-4 flex justify-center" data-live-search-empty-actions>
						<a href="/services/create" class="<?php echo e($primaryButtonClasses); ?>" data-live-search-empty-offer>
							<?php echo $isSearching ? 'Offer this skill' : 'Create first service'; ?> <span aria-hidden="true">-></span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="rounded-3xl border border-neutral-300 bg-surface-card p-6 shadow-soft">
			<h2 class="mb-3 text-2xl font-extrabold text-neutral-950">Inside System Links</h2>
			<div class="flex flex-wrap items-center gap-3">
				<a href="/home" class="<?php echo e($outlineButtonClasses); ?>">Member home</a>
				<?php if ($canOfferSkill): ?>
					<a href="/services/mine" class="<?php echo e($outlineButtonClasses); ?>">My services</a>
				<?php endif; ?>
				<?php if ($canSeekSkill): ?>
					<a href="/bookings/mine" class="<?php echo e($outlineButtonClasses); ?>">My bookings</a>
				<?php endif; ?>
				<?php if ($canOfferSkill): ?>
					<a href="/bookings/provider" class="<?php echo e($outlineButtonClasses); ?>">Provider schedule</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
