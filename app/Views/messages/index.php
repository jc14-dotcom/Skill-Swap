<?php declare(strict_types=1); ?>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Messaging
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Conversations</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Talk directly with your booking counterpart inside each booking thread.</p>
		</header>

		<?php if (($conversations ?? []) === []): ?>
			<div class="rounded-3xl border border-dashed border-neutral-300 bg-white px-6 py-10 text-center shadow-soft">
				<h3 class="text-xl font-heading font-extrabold text-neutral-950">No active conversations yet.</h3>
				<p class="mt-2 text-sm text-neutral-600">Once you have bookings, you can message the other member here.</p>
			</div>
		<?php else: ?>
			<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
				<?php foreach ($conversations as $conversation): ?>
					<?php
					$bookingId = (int) ($conversation['booking_id'] ?? 0);
					$unreadCount = (int) ($conversation['unread_count'] ?? 0);
					$status = (string) ($conversation['status'] ?? 'pending');
					?>
					<article class="grid min-h-[14rem] gap-4 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
						<span class="inline-flex w-fit items-center rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">Status: <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
						<h3 class="text-xl font-heading font-extrabold text-neutral-950"><?php echo htmlspecialchars((string) ($conversation['service_title'] ?? 'Booking'), ENT_QUOTES, 'UTF-8'); ?></h3>
						<p class="text-sm text-neutral-700">With: <?php echo htmlspecialchars((string) ($conversation['counterpart_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="text-sm leading-7 text-neutral-700"><?php echo htmlspecialchars((string) ($conversation['last_message_body'] ?? 'No messages yet.'), ENT_QUOTES, 'UTF-8'); ?></p>

						<div class="mt-auto flex items-center justify-between gap-3">
							<?php if ($unreadCount > 0): ?>
								<span class="inline-flex items-center rounded-full border border-warning-300 bg-warning-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-neutral-900">Unread: <?php echo $unreadCount; ?></span>
							<?php else: ?>
								<span class="text-xs font-semibold uppercase tracking-wide text-neutral-500"><?php echo htmlspecialchars(format_datetime((string) ($conversation['last_message_at'] ?? ''), 'M d, Y H:i'), ENT_QUOTES, 'UTF-8'); ?></span>
							<?php endif; ?>
							<a href="/messages/<?php echo $bookingId; ?>" class="inline-flex items-center gap-1 text-sm font-extrabold text-brand-600 transition hover:text-brand-700">Open chat <span aria-hidden="true">-></span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
