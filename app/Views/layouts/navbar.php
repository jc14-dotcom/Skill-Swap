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
		['label' => 'Marketplace', 'href' => '/services', 'active' => $path === '/services' || preg_match('#^/services/\\d+$#', $path) === 1],
		['label' => 'My Services', 'href' => '/services/mine', 'active' => $path === '/services/mine' || preg_match('#^/services/\\d+/edit$#', $path) === 1],
		['label' => 'My Bookings', 'href' => '/bookings/mine', 'active' => $path === '/bookings/mine' || $path === '/bookings'],
		['label' => 'Messages', 'href' => '/messages', 'active' => str_starts_with($path, '/messages')],
		['label' => 'Dashboard', 'href' => '/dashboard', 'active' => str_starts_with($path, '/dashboard')],
	];
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
?>

<div class="ss-navbar-wrap <?php echo $isAuth ? 'ss-navbar-wrap-app' : ''; ?>">
	<div class="ss-shell">
		<header class="ss-navbar <?php echo $isAuth ? 'ss-navbar-app' : ''; ?>">
			<a href="<?php echo htmlspecialchars($brandHref, ENT_QUOTES, 'UTF-8'); ?>" class="ss-brand" aria-label="SkillSwap home">
				<span class="ss-logo-mark" aria-hidden="true"></span>
				<span>SkillSwap</span>
			</a>

			<ul class="ss-nav-links <?php echo $isAuth ? 'ss-nav-links-app' : ''; ?>" aria-label="Main navigation">
				<?php foreach ($links as $link): ?>
					<li>
						<a href="<?php echo htmlspecialchars((string) $link['href'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $link['active'] ? 'is-active' : ''; ?>">
							<?php echo htmlspecialchars((string) $link['label'], ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="ss-nav-actions <?php echo $isAuth ? 'ss-nav-actions-app' : ''; ?>">
				<?php if (!$isAuth): ?>
					<a href="/login" class="ss-btn-outline">Sign In</a>
					<a href="/register" class="ss-btn">Get Started <span class="ss-arrow">-></span></a>
				<?php else: ?>
					<div class="ss-app-utilities">
						<div class="ss-popover" data-popover data-notif-popover>
							<button type="button" class="ss-icon-btn" data-popover-trigger data-notif-toggle aria-label="Notifications" aria-expanded="false">
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M14.25 18.75C14.25 19.99 13.24 21 12 21C10.76 21 9.75 19.99 9.75 18.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
									<path d="M5.25 9.75C5.25 6.02 8.27 3 12 3C15.73 3 18.75 6.02 18.75 9.75V13.5L20.25 15V15.75H3.75V15L5.25 13.5V9.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
								</svg>
								<?php if ($unreadCount > 0): ?>
									<span class="ss-notif-badge" aria-hidden="true"><?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?></span>
								<?php endif; ?>
							</button>
							<div class="ss-popover-panel ss-notif-panel" data-popover-panel data-notif-panel hidden>
								<div class="ss-popover-head">
									<p class="ss-popover-title">Notifications</p>
									<?php if ($unreadCount > 0): ?>
										<form action="/notifications/read-all" method="post" class="ss-notif-markall-form">
											<?php echo csrf_field(); ?>
											<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo, ENT_QUOTES, 'UTF-8'); ?>">
											<button type="submit" class="ss-notif-markall">Mark all read</button>
										</form>
									<?php endif; ?>
								</div>

								<?php if ($notifications === []): ?>
									<p class="ss-popover-empty">No new notifications yet.</p>
								<?php else: ?>
									<ul class="ss-notif-list">
										<?php foreach ($notifications as $notification): ?>
											<?php
											$notificationId = (int) ($notification['id'] ?? 0);
											$notificationLink = (string) ($notification['link'] ?? '/home');
											if ($notificationLink === '' || !str_starts_with($notificationLink, '/')) {
												$notificationLink = '/home';
											}

											$isUnread = !isset($notification['read_at']) || (string) $notification['read_at'] === '';
											?>
											<li class="ss-notif-item <?php echo $isUnread ? 'is-unread' : ''; ?>">
												<div class="ss-notif-item-head">
													<p class="ss-notif-item-title"><?php echo htmlspecialchars((string) ($notification['title'] ?? 'Notification'), ENT_QUOTES, 'UTF-8'); ?></p>
													<time class="ss-notif-time"><?php echo htmlspecialchars(format_datetime((string) ($notification['created_at'] ?? ''), 'M d, H:i'), ENT_QUOTES, 'UTF-8'); ?></time>
												</div>

												<?php if ((string) ($notification['body'] ?? '') !== ''): ?>
													<p class="ss-notif-item-body"><?php echo htmlspecialchars((string) $notification['body'], ENT_QUOTES, 'UTF-8'); ?></p>
												<?php endif; ?>

												<div class="ss-notif-item-actions">
													<a href="<?php echo htmlspecialchars($notificationLink, ENT_QUOTES, 'UTF-8'); ?>" class="ss-mini-link">Open <span class="ss-arrow">-></span></a>
													<?php if ($isUnread): ?>
														<form action="/notifications/<?php echo $notificationId; ?>/read" method="post" class="ss-notif-read-form">
															<?php echo csrf_field(); ?>
															<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTo, ENT_QUOTES, 'UTF-8'); ?>">
															<button type="submit" class="ss-notif-read-btn">Mark read</button>
														</form>
													<?php endif; ?>
												</div>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</div>

						<div class="ss-popover" data-popover data-profile-popover>
							<button type="button" class="ss-profile-trigger" data-popover-trigger data-profile-toggle aria-label="Profile menu" aria-expanded="false">
								<span class="ss-profile-avatar"><?php echo htmlspecialchars($profileInitial, ENT_QUOTES, 'UTF-8'); ?></span>
								<span class="ss-profile-text">
									<strong><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></strong>
									<small><?php echo htmlspecialchars($roleLabel, ENT_QUOTES, 'UTF-8'); ?></small>
								</span>
								<span class="ss-profile-caret" aria-hidden="true">v</span>
							</button>

							<div class="ss-popover-panel ss-profile-panel" data-popover-panel data-profile-panel hidden>
								<a href="/profile/settings">Profile Settings</a>
								<div class="ss-profile-divider" aria-hidden="true"></div>
								<form action="/logout" method="post" class="ss-profile-logout-form">
									<?php echo csrf_field(); ?>
									<button type="submit" class="ss-profile-logout">Logout</button>
								</form>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<button type="button" class="ss-mobile-toggle" data-mobile-open aria-label="Open menu">=</button>
		</header>
	</div>
</div>

<div class="ss-mobile-overlay" data-mobile-overlay></div>

<aside class="ss-mobile-drawer" data-mobile-drawer>
	<div class="ss-mobile-head">
		<a href="<?php echo htmlspecialchars($brandHref, ENT_QUOTES, 'UTF-8'); ?>" class="ss-brand">
			<span class="ss-logo-mark" aria-hidden="true"></span>
			<span>SkillSwap</span>
		</a>
		<button type="button" class="ss-mobile-close" data-mobile-close aria-label="Close menu">x</button>
	</div>

	<ul class="ss-mobile-links">
		<?php foreach ($links as $link): ?>
			<li>
				<a href="<?php echo htmlspecialchars((string) $link['href'], ENT_QUOTES, 'UTF-8'); ?>">
					<?php echo htmlspecialchars((string) $link['label'], ENT_QUOTES, 'UTF-8'); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="ss-mobile-auth">
		<?php if ($isAuth): ?>
			<a href="/bookings/create" class="ss-btn-soft">Seek Skill</a>
			<a href="/services/create" class="ss-btn-outline">Offer Skill</a>
			<a href="/services/mine" class="ss-btn-outline">My Services</a>
			<a href="/bookings/mine" class="ss-btn-outline">My Booking Requests</a>
			<a href="/bookings/provider" class="ss-btn-outline">Provider Queue</a>
			<form action="/logout" method="post" class="ss-logout-form">
				<?php echo csrf_field(); ?>
				<button type="submit" class="ss-btn-outline" style="width: 100%;">Logout</button>
			</form>
		<?php else: ?>
			<a href="/login" class="ss-btn-soft">Sign In</a>
			<a href="/register" class="ss-btn-outline">Create Account</a>
		<?php endif; ?>
	</div>

	<div class="ss-mobile-contact">
		<?php if ($isAuth): ?>
			<h4>Inside SkillSwap</h4>
			<p>Every member can seek and offer skills.</p>
			<p>Use My Services and My Bookings to track your swaps.</p>
		<?php else: ?>
			<h4>Contact Info</h4>
			<p>support@skillswap.local</p>
			<p>+63 2 555 0112</p>
			<p>Trusted bookings, verified providers, secure sessions.</p>
		<?php endif; ?>
	</div>
</aside>
