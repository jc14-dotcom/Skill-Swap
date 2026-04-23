<?php declare(strict_types=1); ?>

<?php $oldRating = (string) old_input('rating', '5'); ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Feedback
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Submit A Booking Review</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Reviews are available after booking completion and visible for platform trust.</p>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
			<form action="/reviews" method="post" class="space-y-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<?php echo csrf_field(); ?>

				<div class="grid gap-2" data-field>
					<label for="booking_id" class="text-sm font-extrabold text-neutral-800">Booking ID</label>
					<input id="booking_id" name="booking_id" type="number" min="1" required value="<?php echo (int) old_input('booking_id', (int) ($bookingId ?? 0)); ?>" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
				</div>

				<div class="grid gap-2" data-field>
					<label for="rating" class="text-sm font-extrabold text-neutral-800">Rating</label>
					<select id="rating" name="rating" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
						<?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
							<option value="<?php echo $rating; ?>" <?php echo $oldRating === (string) $rating ? 'selected' : ''; ?>>
								<?php echo $rating; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grid gap-2" data-field>
					<label for="comment" class="text-sm font-extrabold text-neutral-800">Comment (optional)</label>
					<textarea id="comment" name="comment" rows="5" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-7 text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><?php echo htmlspecialchars((string) old_input('comment'), ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="flex flex-wrap gap-3 pt-1">
					<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Submit Review <span aria-hidden="true">-></span></button>
					<a href="/bookings/mine" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Back to my bookings</a>
				</div>
			</form>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
					<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Review Quality</span>
					<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Give feedback that helps the next member.</h2>
					<p class="mt-2 text-sm leading-7 text-neutral-700">Balanced, specific reviews improve trust and help providers and seekers work better together.</p>
					<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
						<li>Describe outcomes, not just opinions.</li>
						<li>Mention communication and delivery quality.</li>
						<li>Keep tone professional and constructive.</li>
					</ul>
				</section>

				<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
					<h3 class="text-base font-heading font-extrabold text-neutral-950">Submission Snapshot</h3>
					<div class="mt-3 space-y-2 text-sm">
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Booking reference</span><strong class="font-extrabold text-neutral-900">#<?php echo (int) old_input('booking_id', (int) ($bookingId ?? 0)); ?></strong></div>
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Selected rating</span><strong class="font-extrabold text-neutral-900"><?php echo htmlspecialchars($oldRating, ENT_QUOTES, 'UTF-8'); ?>/5</strong></div>
					</div>
				</section>
			</aside>
		</div>
	</div>
</section>
