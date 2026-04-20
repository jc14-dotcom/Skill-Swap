<?php
declare(strict_types=1);

$user = auth_user();
$isAuth = is_array($user);
$path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/services'), PHP_URL_PATH);
$path = is_string($path) && $path !== '' ? $path : '/services';

$displayName = trim((string) ($user['name'] ?? 'Member'));
if ($displayName === '') {
	$displayName = 'Member';
}

$profileInitial = strtoupper(substr($displayName, 0, 1));
$roleLabel = strtoupper((string) ($user['role'] ?? 'member'));
$userRole = strtolower((string) ($user['role'] ?? 'member'));
$canSeekSkill = !$isAuth || $userRole !== 'provider';
$canOfferSkill = !$isAuth || $userRole !== 'seeker';

$notificationFeed = [
	'unread_count' => 0,
	'items' => [],
];

$redirectTo = (string) ($_SERVER['REQUEST_URI'] ?? '/home');
if ($redirectTo === '' || !str_starts_with($redirectTo, '/')) {
	$redirectTo = '/home';
}

if ($isAuth) {
	try {
		$notificationFeed = (new \App\Services\NotificationService())->feed($user, 6);
	} catch (\Throwable $exception) {
		$notificationFeed = [
			'unread_count' => 0,
			'items' => [],
		];
	}
}

$unreadCount = (int) ($notificationFeed['unread_count'] ?? 0);
$notifications = is_array($notificationFeed['items'] ?? null) ? $notificationFeed['items'] : [];

$brandHref = $isAuth ? '/home' : '/services';

if ($isAuth) {
	$links = [
		['label' => 'Home', 'href' => '/home', 'active' => $path === '/home'],
	];

	if ($canSeekSkill) {
		$links[] = ['label' => 'Seek Skills', 'href' => '/services', 'active' => $path === '/services' || preg_match('#^/services/\\d+$#', $path) === 1];
	}

	$links[] = ['label' => 'Messages', 'href' => '/messages', 'active' => str_starts_with($path, '/messages')];
	$links[] = ['label' => 'Dashboard', 'href' => '/dashboard', 'active' => str_starts_with($path, '/dashboard')];

	if ($canOfferSkill) {
		$links[] = ['label' => 'Provider Schedule', 'href' => '/bookings/provider', 'active' => $path === '/bookings/provider'];
		$links[] = ['label' => 'My Services', 'href' => '/services/mine', 'active' => $path === '/services/mine' || preg_match('#^/services/\\d+/edit$#', $path) === 1];
	}

	if ($canSeekSkill) {
		$links[] = ['label' => 'My Bookings', 'href' => '/bookings/mine', 'active' => $path === '/bookings/mine' || $path === '/bookings'];
	}
} else {
	$links = [
		['label' => 'Home', 'href' => '/services#home', 'active' => $path === '/' || $path === '/services'],
		['label' => 'Marketplace', 'href' => '/services#marketplace', 'active' => $path === '/services'],
		['label' => 'About', 'href' => '/services#about', 'active' => false],
		['label' => 'Workflow', 'href' => '/services#workflow', 'active' => false],
		['label' => 'Stories', 'href' => '/services#stories', 'active' => false],
		['label' => 'Contact', 'href' => '/services#footer', 'active' => false],
	];
}

$outlineButtonClasses = 'inline-flex items-center justify-center rounded-full border border-neutral-400 bg-white px-4 py-2 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700';
$primaryButtonClasses = 'inline-flex items-center justify-center gap-2 rounded-full bg-brand-500 px-4 py-2 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600';
$miniLinkClasses = 'inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700';
$mobileNavLinkClasses = 'block rounded-xl px-3 py-2 text-sm font-bold text-neutral-700 transition hover:bg-brand-50 hover:text-brand-700';
$guestOutlineButtonClasses = 'inline-flex items-center justify-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-base font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700';
$guestPrimaryButtonClasses = 'inline-flex items-center justify-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-base font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600';

$navbarWrapClasses = $isAuth
	? 'sticky top-0 z-40 border-b border-neutral-200 bg-white/95 backdrop-blur'
	: 'sticky top-0 z-40 bg-surface-page/95 py-3 backdrop-blur';
$navbarInnerClasses = $isAuth
	? 'mx-auto flex w-[min(1320px,calc(100%-2rem))] items-center gap-4 py-3'
	: 'mx-auto flex w-[min(1320px,calc(100%-2rem))] items-center gap-6 rounded-full border border-neutral-200 bg-white px-6 py-4 shadow-soft';
$brandClasses = $isAuth
	? 'inline-flex items-center gap-2 text-xl font-heading font-extrabold tracking-tight text-neutral-950'
	: 'inline-flex items-center gap-2 text-[2rem] font-heading font-extrabold tracking-tight text-neutral-950';
$navListClasses = $isAuth
	? 'hidden items-center gap-1 xl:flex'
	: 'hidden items-center gap-1.5 xl:flex';
$navActionsClasses = $isAuth
	? 'ml-auto hidden items-center gap-2 xl:flex'
	: 'ml-auto hidden items-center gap-3 xl:flex';
$mobileToggleClasses = $isAuth
	? 'ml-auto inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-xl font-black leading-none text-neutral-700 xl:hidden'
	: 'ml-auto inline-flex h-11 w-11 items-center justify-center rounded-full border border-neutral-300 bg-white text-xl font-black leading-none text-neutral-700 xl:hidden';
?>

<div class="<?php echo htmlspecialchars($navbarWrapClasses, ENT_QUOTES, 'UTF-8'); ?>">
	<div class="<?php echo htmlspecialchars($navbarInnerClasses, ENT_QUOTES, 'UTF-8'); ?>">
		<a href="<?php echo htmlspecialchars($brandHref, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($brandClasses, ENT_QUOTES, 'UTF-8'); ?>" aria-label="SkillSwap home">
			<span class="h-3.5 w-3.5 rounded-sm bg-gradient-to-br from-brand-500 to-brand-700" aria-hidden="true"></span>
			<span>SkillSwap</span>
		</a>

		<ul class="<?php echo htmlspecialchars($navListClasses, ENT_QUOTES, 'UTF-8'); ?>" aria-label="Main navigation">
			<?php foreach ($links as $link): ?>
				<?php
				if ($isAuth) {
					$linkClasses = $link['active']
						? 'rounded-full bg-brand-50 px-3 py-2 text-sm font-bold text-brand-700'
						: 'rounded-full px-3 py-2 text-sm font-bold text-neutral-700 transition hover:bg-brand-50 hover:text-brand-700';
				} else {
					$linkClasses = $link['active']
						? 'rounded-full bg-brand-100 px-4 py-2.5 text-base font-extrabold text-brand-700'
						: 'rounded-full px-4 py-2.5 text-base font-extrabold text-neutral-600 transition hover:bg-brand-50 hover:text-brand-700';
				}
				?>
				<li>
					<a href="<?php echo htmlspecialchars((string) $link['href'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($linkClasses, ENT_QUOTES, 'UTF-8'); ?>">
						<?php echo htmlspecialchars((string) $link['label'], ENT_QUOTES, 'UTF-8'); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="<?php echo htmlspecialchars($navActionsClasses, ENT_QUOTES, 'UTF-8'); ?>">
			<?php if (!$isAuth): ?>
				<a href="/login" class="<?php echo htmlspecialchars($guestOutlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Sign In</a>
				<a href="/register" class="<?php echo htmlspecialchars($guestPrimaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Get Started <span aria-hidden="true">-></span></a>
			<?php else: ?>
				<div class="flex items-center gap-2">
					<div class="relative" data-popover data-notif-popover>
						<button type="button" class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-neutral-700 transition hover:border-brand-500 hover:text-brand-700" data-popover-trigger data-notif-toggle aria-label="Notifications" aria-expanded="false">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-5 w-5">
								<path d="M14.25 18.75C14.25 19.99 13.24 21 12 21C10.76 21 9.75 19.99 9.75 18.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
								<path d="M5.25 9.75C5.25 6.02 8.27 3 12 3C15.73 3 18.75 6.02 18.75 9.75V13.5L20.25 15V15.75H3.75V15L5.25 13.5V9.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
							</svg>
							<?php if ($unreadCount > 0): ?>
								<span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-danger-500 px-1 text-[10px] font-extrabold text-white" aria-hidden="true"><?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?></span>
							<?php endif; ?>
						</button>

						<div class="absolute right-0 top-[calc(100%+0.5rem)] z-50 w-[min(24rem,92vw)] rounded-2xl border border-neutral-300 bg-white p-3 shadow-card" data-popover-panel data-notif-panel hidden>
							<div class="mb-2 flex items-center justify-between gap-2">
								<p class="text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">Notifications</p>
								<?php if ($unreadCount > 0): ?>
									<form action="/notifications/read-all" method="post">
										<?php echo csrf_field(); ?>
										<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo, ENT_QUOTES, 'UTF-8'); ?>">
										<button type="submit" class="text-xs font-bold text-neutral-700 transition hover:text-brand-700">Mark all read</button>
									</form>
								<?php endif; ?>
							</div>

							<?php if ($notifications === []): ?>
								<p class="rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-4 text-sm text-neutral-600">No new notifications yet.</p>
							<?php else: ?>
								<ul class="grid max-h-[24rem] gap-2 overflow-y-auto pr-1">
									<?php foreach ($notifications as $notification): ?>
										<?php
										$notificationId = (int) ($notification['id'] ?? 0);
										$notificationLink = (string) ($notification['link'] ?? '/home');
										if ($notificationLink === '' || !str_starts_with($notificationLink, '/')) {
											$notificationLink = '/home';
										}

										$isUnread = !isset($notification['read_at']) || (string) $notification['read_at'] === '';
										$itemClasses = $isUnread
											? 'rounded-xl border border-brand-200 bg-surface-page p-3'
											: 'rounded-xl border border-neutral-200 bg-neutral-50 p-3';
										?>
										<li class="<?php echo htmlspecialchars($itemClasses, ENT_QUOTES, 'UTF-8'); ?>">
											<div class="flex items-start justify-between gap-2">
												<p class="text-sm font-extrabold text-neutral-900"><?php echo htmlspecialchars((string) ($notification['title'] ?? 'Notification'), ENT_QUOTES, 'UTF-8'); ?></p>
												<time class="text-xs font-semibold text-neutral-500"><?php echo htmlspecialchars(format_datetime((string) ($notification['created_at'] ?? ''), 'M d, H:i'), ENT_QUOTES, 'UTF-8'); ?></time>
											</div>

											<?php if ((string) ($notification['body'] ?? '') !== ''): ?>
												<p class="mt-1 text-sm leading-6 text-neutral-700"><?php echo htmlspecialchars((string) $notification['body'], ENT_QUOTES, 'UTF-8'); ?></p>
											<?php endif; ?>

											<div class="mt-2 flex items-center gap-3">
												<a href="<?php echo htmlspecialchars($notificationLink, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($miniLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">Open <span aria-hidden="true">-></span></a>
												<?php if ($isUnread): ?>
													<form action="/notifications/<?php echo $notificationId; ?>/read" method="post">
														<?php echo csrf_field(); ?>
														<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo, ENT_QUOTES, 'UTF-8'); ?>">
														<button type="submit" class="text-xs font-bold text-neutral-700 transition hover:text-brand-700">Mark read</button>
													</form>
												<?php endif; ?>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					</div>

					<div class="relative" data-popover data-profile-popover>
						<button type="button" class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-2 py-1.5 transition hover:border-brand-500" data-popover-trigger data-profile-toggle aria-label="Profile menu" aria-expanded="false">
							<span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700"><?php echo htmlspecialchars($profileInitial, ENT_QUOTES, 'UTF-8'); ?></span>
							<span class="hidden text-left md:flex md:flex-col md:items-start md:leading-tight">
								<strong class="text-sm font-extrabold text-neutral-900"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></strong>
								<small class="text-xs font-bold uppercase tracking-wide text-neutral-500"><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></small>
							</span>
							<span class="text-xs font-black text-neutral-500" aria-hidden="true">v</span>
						</button>

						<div class="absolute right-0 top-[calc(100%+0.5rem)] z-50 w-52 rounded-2xl border border-neutral-300 bg-white p-2 shadow-card" data-popover-panel data-profile-panel hidden>
							<a href="/profile/settings" class="block rounded-xl px-3 py-2 text-sm font-bold text-neutral-700 transition hover:bg-brand-50 hover:text-brand-700">Profile Settings</a>
							<div class="my-1 h-px bg-neutral-200" aria-hidden="true"></div>
							<form action="/logout" method="post">
								<?php echo csrf_field(); ?>
								<button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-bold text-danger-700 transition hover:bg-danger-50">Logout</button>
							</form>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<button type="button" class="<?php echo htmlspecialchars($mobileToggleClasses, ENT_QUOTES, 'UTF-8'); ?>" data-mobile-open aria-label="Open menu">=</button>
	</div>
</div>

<div class="fixed inset-0 z-40 bg-neutral-950/45 opacity-0 pointer-events-none transition xl:hidden" data-mobile-overlay></div>

<aside class="fixed right-0 top-0 z-50 h-full w-[min(22rem,88vw)] translate-x-full pointer-events-none overflow-y-auto border-l border-neutral-200 bg-white p-5 shadow-card transition xl:hidden" data-mobile-drawer>
	<div class="flex items-center justify-between gap-3">
		<a href="<?php echo htmlspecialchars($brandHref, ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center gap-2 text-xl font-heading font-extrabold tracking-tight text-neutral-950">
			<span class="h-3.5 w-3.5 rounded-sm bg-gradient-to-br from-brand-500 to-brand-700" aria-hidden="true"></span>
			<span>SkillSwap</span>
			</a>
		<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-300 bg-white text-lg font-black text-neutral-700" data-mobile-close aria-label="Close menu">x</button>
	</div>

	<ul class="mt-5 grid gap-1">
		<?php foreach ($links as $link): ?>
			<li>
				<a href="<?php echo htmlspecialchars((string) $link['href'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($mobileNavLinkClasses, ENT_QUOTES, 'UTF-8'); ?>">
					<?php echo htmlspecialchars((string) $link['label'], ENT_QUOTES, 'UTF-8'); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="mt-5 grid gap-2">
		<?php if ($isAuth): ?>
			<?php if ($canSeekSkill): ?>
				<a href="/services" class="w-full <?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Seek Skill</a>
				<a href="/bookings/mine" class="w-full <?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">My Booking Requests</a>
			<?php endif; ?>

			<?php if ($canOfferSkill): ?>
				<a href="/services/create" class="w-full <?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Offer Skill</a>
				<a href="/services/mine" class="w-full <?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">My Services</a>
			<?php endif; ?>

			<?php if ($canOfferSkill): ?>
				<a href="/bookings/provider" class="w-full <?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Provider Schedule</a>
			<?php endif; ?>
			<form action="/logout" method="post">
				<?php echo csrf_field(); ?>
				<button type="submit" class="w-full <?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Logout</button>
			</form>
		<?php else: ?>
			<a href="/login" class="w-full <?php echo htmlspecialchars($primaryButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Sign In</a>
			<a href="/register" class="w-full <?php echo htmlspecialchars($outlineButtonClasses, ENT_QUOTES, 'UTF-8'); ?>">Create Account</a>
		<?php endif; ?>
	</div>

	<div class="mt-5 rounded-2xl border border-neutral-200 bg-surface-page px-4 py-3">
		<?php if ($isAuth): ?>
			<h4 class="text-sm font-extrabold uppercase tracking-wide text-brand-700">Inside SkillSwap</h4>
			<p class="mt-2 text-sm text-neutral-700">Every member can seek and offer skills.</p>
			<p class="text-sm text-neutral-700">Use My Services and My Bookings to track your swaps.</p>
		<?php else: ?>
			<h4 class="text-sm font-extrabold uppercase tracking-wide text-brand-700">Contact Info</h4>
			<p class="mt-2 text-sm text-neutral-700">support@skillswap.local</p>
			<p class="text-sm text-neutral-700">+63 2 555 0112</p>
			<p class="text-sm text-neutral-700">Trusted bookings, verified providers, secure sessions.</p>
		<?php endif; ?>
	</div>
</aside>
