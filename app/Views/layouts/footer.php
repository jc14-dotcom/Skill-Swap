<?php declare(strict_types=1); ?>

<?php if (auth_check()): ?>
	<footer class="mt-10 border-t border-neutral-200 bg-white/90" id="footer">
		<div class="mx-auto flex w-[min(1320px,calc(100%-2rem))] flex-wrap items-center justify-between gap-3 py-5 text-sm text-neutral-600">
			<p class="font-semibold">&copy; SkillSwap <span data-current-year>2026</span> | In-system workspace</p>
			<nav class="flex flex-wrap items-center gap-3">
				<a href="/home" class="font-semibold transition hover:text-brand-700">Home</a>
				<a href="/services" class="font-semibold transition hover:text-brand-700">Marketplace</a>
				<a href="/services/mine" class="font-semibold transition hover:text-brand-700">My Services</a>
				<a href="/bookings/mine" class="font-semibold transition hover:text-brand-700">My Bookings</a>
				<a href="/dashboard" class="font-semibold transition hover:text-brand-700">Dashboard</a>
			</nav>
		</div>
	</footer>
<?php else: ?>
	<footer class="mt-12 bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 text-brand-50" id="footer">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))] py-10">
			<div class="mb-8 flex flex-wrap items-center justify-between gap-4 border-b border-overlay-white18 pb-6">
				<a href="/services" class="inline-flex items-center gap-2 text-2xl font-heading font-extrabold tracking-tight text-white">
					<span class="h-3.5 w-3.5 rounded-sm bg-white" aria-hidden="true"></span>
					<span>SkillSwap</span>
				</a>

				<div class="flex items-center gap-2 text-sm" aria-label="Social links">
					<span class="font-bold text-brand-100">Follow us:</span>
					<a href="#" aria-label="Facebook" class="inline-flex h-8 min-w-8 items-center justify-center rounded-full border border-overlay-white35 px-2 font-extrabold transition hover:bg-overlay-white18">f</a>
					<a href="#" aria-label="Twitter" class="inline-flex h-8 min-w-8 items-center justify-center rounded-full border border-overlay-white35 px-2 font-extrabold transition hover:bg-overlay-white18">t</a>
					<a href="#" aria-label="LinkedIn" class="inline-flex h-8 min-w-8 items-center justify-center rounded-full border border-overlay-white35 px-2 font-extrabold transition hover:bg-overlay-white18">in</a>
					<a href="#" aria-label="YouTube" class="inline-flex h-8 min-w-8 items-center justify-center rounded-full border border-overlay-white35 px-2 font-extrabold transition hover:bg-overlay-white18">yt</a>
				</div>
			</div>

			<div class="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
				<div>
					<h4 class="text-base font-extrabold uppercase tracking-wide text-brand-100">Recent Skill Stories</h4>
					<div class="mt-3 grid gap-3">
						<div class="flex items-start gap-3 rounded-2xl border border-overlay-white18 bg-overlay-white18 p-3">
							<div class="h-10 w-10 rounded-xl bg-overlay-white35" aria-hidden="true"></div>
							<div>
								<p class="text-sm font-semibold text-white">How to package your first skill offer</p>
								<p class="text-xs text-brand-100">April 2026</p>
							</div>
						</div>
						<div class="flex items-start gap-3 rounded-2xl border border-overlay-white18 bg-overlay-white18 p-3">
							<div class="h-10 w-10 rounded-xl bg-overlay-white35" aria-hidden="true"></div>
							<div>
								<p class="text-sm font-semibold text-white">From seeker to provider: growth path</p>
								<p class="text-xs text-brand-100">April 2026</p>
							</div>
						</div>
					</div>
				</div>

				<div>
					<h4 class="text-base font-extrabold uppercase tracking-wide text-brand-100">Marketplace</h4>
					<ul class="mt-3 grid gap-2 text-sm">
						<li><a href="/services" class="font-semibold text-white transition hover:text-brand-100">Browse skills</a></li>
						<li><a href="/services/create" class="font-semibold text-white transition hover:text-brand-100">Publish service</a></li>
						<li><a href="/services" class="font-semibold text-white transition hover:text-brand-100">Create booking</a></li>
						<li><a href="/bookings/mine" class="font-semibold text-white transition hover:text-brand-100">Track requests</a></li>
						<li><a href="/reviews/create" class="font-semibold text-white transition hover:text-brand-100">Leave a review</a></li>
					</ul>
				</div>

				<div>
					<h4 class="text-base font-extrabold uppercase tracking-wide text-brand-100">Get Free Link</h4>
					<ul class="mt-3 grid gap-2 text-sm">
						<li><a href="/services#about" class="font-semibold text-white transition hover:text-brand-100">About SkillSwap</a></li>
						<li><a href="/services#marketplace" class="font-semibold text-white transition hover:text-brand-100">Top services</a></li>
						<li><a href="/services#workflow" class="font-semibold text-white transition hover:text-brand-100">How it works</a></li>
						<li><a href="/services#stories" class="font-semibold text-white transition hover:text-brand-100">Community stories</a></li>
						<li><a href="/dashboard" class="font-semibold text-white transition hover:text-brand-100">Member dashboard</a></li>
					</ul>
				</div>

				<div>
					<h4 class="text-base font-extrabold uppercase tracking-wide text-brand-100">Newsletter</h4>
					<p class="mt-3 text-sm leading-6 text-brand-100">Get product updates, booking tips, and provider growth ideas every month.</p>
					<form class="mt-3 flex items-center gap-2" action="#" method="post" onsubmit="return false;">
						<input type="email" name="email" placeholder="Email here" aria-label="Email" class="h-10 w-full rounded-full border border-overlay-white35 bg-overlay-white18 px-4 text-sm font-semibold text-white placeholder:text-brand-100 focus:outline-none focus:ring-2 focus:ring-white">
						<button type="submit" aria-label="Send" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-white text-lg font-black text-brand-700 transition hover:bg-brand-100">&gt;</button>
					</form>
				</div>
			</div>

			<div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-overlay-white18 pt-5 text-sm text-brand-100">
				<p>&copy; SkillSwap <span data-current-year>2026</span> | All rights reserved</p>
				<nav class="flex flex-wrap items-center gap-3">
					<a href="#" class="font-semibold transition hover:text-white">Terms</a>
					<a href="#" class="font-semibold transition hover:text-white">Privacy</a>
					<a href="/services#footer" class="font-semibold transition hover:text-white">Contact</a>
				</nav>
			</div>
		</div>
	</footer>
<?php endif; ?>
