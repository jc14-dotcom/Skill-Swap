<?php declare(strict_types=1); ?>

<?php
$threadData = is_array($thread ?? null) ? $thread : [];
$booking = is_array($threadData['booking'] ?? null) ? $threadData['booking'] : [];
$messages = is_array($threadData['messages'] ?? null) ? $threadData['messages'] : [];
$counterpart = is_array($threadData['counterpart'] ?? null) ? $threadData['counterpart'] : [];
$actorData = is_array($actor ?? null) ? $actor : [];

$bookingId = (int) ($booking['id'] ?? 0);
$actorId = (int) ($actorData['id'] ?? 0);
?>

<section class="py-14 md:py-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="flex flex-col gap-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8 lg:flex-row lg:items-end lg:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Messaging
				</span>
				<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Conversation for <?php echo htmlspecialchars((string) ($booking['service_title'] ?? 'Booking'), ENT_QUOTES, 'UTF-8'); ?></h1>
				<p class="mt-3 text-sm leading-7 text-neutral-700">
					Talking with <?php echo htmlspecialchars((string) ($counterpart['name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?>
					· Status: <?php echo htmlspecialchars((string) ($booking['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?>
				</p>
			</div>
			<a href="/messages" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Back to conversations</a>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
			<div class="space-y-4">
				<div class="rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
					<?php if ($messages === []): ?>
						<div class="rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 px-5 py-8 text-center">
							<h3 class="text-lg font-heading font-extrabold text-neutral-950">No messages yet.</h3>
							<p class="mt-2 text-sm text-neutral-600">Send the first message to start coordinating this booking.</p>
						</div>
					<?php else: ?>
						<div class="grid max-h-[36rem] gap-3 overflow-y-auto pr-1">
							<?php foreach ($messages as $message): ?>
								<?php $isOwn = (int) ($message['sender_id'] ?? 0) === $actorId; ?>
								<article class="<?php echo $isOwn ? 'ml-auto max-w-[88%] rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3' : 'max-w-[88%] rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3'; ?>">
									<p class="text-sm leading-7 text-neutral-800"><?php echo nl2br(htmlspecialchars((string) ($message['body'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></p>
									<div class="mt-2 flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-wide text-neutral-500">
										<span><?php echo htmlspecialchars((string) ($message['sender_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></span>
										<span><?php echo htmlspecialchars(format_datetime((string) ($message['created_at'] ?? ''), 'M d, H:i'), ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<form action="/messages/<?php echo $bookingId; ?>" method="post" class="space-y-3 rounded-3xl border border-neutral-200 bg-white p-5 shadow-soft">
					<?php echo csrf_field(); ?>
					<label for="message-body" class="text-xs font-extrabold uppercase tracking-[0.12em] text-neutral-600">Write a message</label>
					<textarea id="message-body" name="body" rows="4" maxlength="5000" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-7 text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Type your message here..."><?php echo htmlspecialchars((string) old_input('body', ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
					<div class="flex justify-end">
						<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Send message <span aria-hidden="true">-></span></button>
					</div>
				</form>
			</div>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
					<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Conversation Flow</span>
					<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Keep updates short and actionable.</h2>
					<p class="mt-2 text-sm leading-7 text-neutral-700">Clear check-ins reduce delivery delays and make booking outcomes easier to track.</p>
					<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
						<li>Confirm scope and timeline in plain terms.</li>
						<li>Share file links before requesting revisions.</li>
						<li>Summarize agreed next steps after each milestone.</li>
					</ul>
				</section>

				<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
					<h3 class="text-base font-heading font-extrabold text-neutral-950">Booking Context</h3>
					<div class="mt-3 space-y-2 text-sm">
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Booking ID</span><strong class="font-extrabold text-neutral-900">#<?php echo $bookingId; ?></strong></div>
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Counterpart</span><strong class="font-extrabold text-neutral-900"><?php echo htmlspecialchars((string) ($counterpart['name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Status</span><strong class="font-extrabold text-neutral-900"><?php echo htmlspecialchars((string) ($booking['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
					</div>
				</section>
			</aside>
		</div>
	</div>
</section>
