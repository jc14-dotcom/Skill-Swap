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

<section class="ss-section">
	<div class="ss-shell ss-utility-stack">
		<div class="ss-page-head">
			<div>
				<span class="ss-section-label">Messaging</span>
				<h1 class="ss-page-title">Conversation for <?php echo htmlspecialchars((string) ($booking['service_title'] ?? 'Booking'), ENT_QUOTES, 'UTF-8'); ?></h1>
				<p class="ss-muted-block" style="margin-top: 0.4rem;">
					Talking with <?php echo htmlspecialchars((string) ($counterpart['name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?>
					· Status: <?php echo htmlspecialchars((string) ($booking['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?>
				</p>
			</div>
			<a href="/messages" class="ss-btn-outline">Back to conversations</a>
		</div>

		<div class="ss-chat-layout">
			<div class="ss-chat-main">
				<div class="ss-chat-thread-wrap">
					<?php if ($messages === []): ?>
						<div class="ss-empty-card" style="margin: 0;">
							<h3>No messages yet.</h3>
							<p>Send the first message to start coordinating this booking.</p>
						</div>
					<?php else: ?>
						<div class="ss-chat-thread">
							<?php foreach ($messages as $message): ?>
								<?php $isOwn = (int) ($message['sender_id'] ?? 0) === $actorId; ?>
								<article class="ss-chat-bubble <?php echo $isOwn ? 'ss-chat-bubble-own' : ''; ?>">
									<p><?php echo nl2br(htmlspecialchars((string) ($message['body'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></p>
									<div class="ss-chat-meta">
										<span><?php echo htmlspecialchars((string) ($message['sender_name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></span>
										<span><?php echo htmlspecialchars(format_datetime((string) ($message['created_at'] ?? ''), 'M d, H:i'), ENT_QUOTES, 'UTF-8'); ?></span>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<form action="/messages/<?php echo $bookingId; ?>" method="post" class="ss-chat-composer">
					<?php echo csrf_field(); ?>
					<label for="message-body" class="ss-subtle">Write a message</label>
					<textarea id="message-body" name="body" rows="4" maxlength="5000" class="ss-chat-input" placeholder="Type your message here..."><?php echo htmlspecialchars((string) old_input('body', ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
					<div class="ss-record-actions" style="justify-content: flex-end; margin-top: 0.4rem;">
						<button type="submit" class="ss-btn">Send message <span class="ss-arrow">-></span></button>
					</div>
				</form>
			</div>

			<aside class="ss-chat-aside">
				<section class="ss-accent-panel">
					<span class="ss-accent-tag">Conversation Flow</span>
					<h2>Keep updates short and actionable.</h2>
					<p>Clear check-ins reduce delivery delays and make booking outcomes easier to track.</p>
					<ul class="ss-accent-list">
						<li>Confirm scope and timeline in plain terms.</li>
						<li>Share file links before requesting revisions.</li>
						<li>Summarize agreed next steps after each milestone.</li>
					</ul>
				</section>

				<section class="ss-page-card ss-mini-info-card">
					<h3 style="font-size: 1rem;">Booking Context</h3>
					<div class="ss-mini-info-row"><span>Booking ID</span><strong>#<?php echo $bookingId; ?></strong></div>
					<div class="ss-mini-info-row"><span>Counterpart</span><strong><?php echo htmlspecialchars((string) ($counterpart['name'] ?? 'Member'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
					<div class="ss-mini-info-row"><span>Status</span><strong><?php echo htmlspecialchars((string) ($booking['status'] ?? 'pending'), ENT_QUOTES, 'UTF-8'); ?></strong></div>
				</section>
			</aside>
		</div>
	</div>
</section>
