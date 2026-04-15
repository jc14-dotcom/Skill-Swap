<?php declare(strict_types=1); ?>

<?php if (auth_check()): ?>
	<footer class="ss-site-footer" id="footer">
		<div class="ss-shell">
			<div class="ss-footer-bottom" style="border-top: 0; padding-top: 0;">
				<p>&copy; SkillSwap <span id="ss-year">2026</span> | In-system workspace</p>
				<nav>
					<a href="/home">Home</a>
					<a href="/services">Marketplace</a>
					<a href="/services/mine">My Services</a>
					<a href="/bookings/mine">My Bookings</a>
					<a href="/dashboard">Dashboard</a>
				</nav>
			</div>
		</div>
	</footer>
<?php else: ?>
	<footer class="ss-site-footer" id="footer">
		<div class="ss-shell">
			<div class="ss-footer-top">
				<a href="/services" class="ss-brand">
					<span class="ss-logo-mark" aria-hidden="true"></span>
					<span>SkillSwap</span>
				</a>

				<div class="ss-footer-social" aria-label="Social links">
					<span>Follow us:</span>
					<a href="#" aria-label="Facebook">f</a>
					<a href="#" aria-label="Twitter">t</a>
					<a href="#" aria-label="LinkedIn">in</a>
					<a href="#" aria-label="YouTube">yt</a>
				</div>
			</div>

			<div class="ss-footer-grid">
				<div class="ss-footer-col">
					<h4>Recent Skill Stories</h4>
					<div class="ss-footer-mini">
						<div class="ss-footer-post">
							<div class="ss-footer-thumb" aria-hidden="true"></div>
							<div>
								<p>How to package your first skill offer</p>
								<p class="ss-subtle">April 2026</p>
							</div>
						</div>
						<div class="ss-footer-post">
							<div class="ss-footer-thumb" aria-hidden="true"></div>
							<div>
								<p>From seeker to provider: growth path</p>
								<p class="ss-subtle">April 2026</p>
							</div>
						</div>
					</div>
				</div>

				<div class="ss-footer-col">
					<h4>Marketplace</h4>
					<ul>
						<li><a href="/services">Browse skills</a></li>
						<li><a href="/services/create">Publish service</a></li>
						<li><a href="/services">Create booking</a></li>
						<li><a href="/bookings/mine">Track requests</a></li>
						<li><a href="/reviews/create">Leave a review</a></li>
					</ul>
				</div>

				<div class="ss-footer-col">
					<h4>Get Free Link</h4>
					<ul>
						<li><a href="/services#about">About SkillSwap</a></li>
						<li><a href="/services#marketplace">Top services</a></li>
						<li><a href="/services#workflow">How it works</a></li>
						<li><a href="/services#stories">Community stories</a></li>
						<li><a href="/dashboard">Member dashboard</a></li>
					</ul>
				</div>

				<div class="ss-footer-col">
					<h4>Newsletter</h4>
					<p>Get product updates, booking tips, and provider growth ideas every month.</p>
					<form class="ss-footer-news" action="#" method="post" onsubmit="return false;">
						<input type="email" name="email" placeholder="Email here" aria-label="Email">
						<button type="submit" aria-label="Send">&gt;</button>
					</form>
				</div>
			</div>

			<div class="ss-footer-bottom">
				<p>&copy; SkillSwap <span id="ss-year">2026</span> | All rights reserved</p>
				<nav>
					<a href="#">Terms</a>
					<a href="#">Privacy</a>
					<a href="/services#footer">Contact</a>
				</nav>
			</div>
		</div>
	</footer>
<?php endif; ?>
