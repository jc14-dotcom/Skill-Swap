<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">Provider Bookings</h1>

<?php if (($bookings ?? []) === []): ?>
	<p class="text-slate-600">No incoming booking requests.</p>
<?php else: ?>
	<div class="space-y-4">
		<?php foreach ($bookings as $booking): ?>
			<article class="rounded border border-slate-200 bg-white p-4">
				<div class="flex flex-wrap items-center justify-between gap-3">
					<div>
						<h2 class="text-lg font-semibold"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></h2>
						<p class="text-sm text-slate-600">Seeker: <?php echo htmlspecialchars((string) ($booking['seeker_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
						<p class="text-sm text-slate-600">Status: <?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></p>
					</div>

					<div class="flex flex-wrap gap-2">
						<?php foreach (['accepted', 'rejected', 'completed', 'cancelled'] as $status): ?>
							<form action="/bookings/<?php echo (int) $booking['id']; ?>/status" method="post">
								<?php echo csrf_field(); ?>
								<input type="hidden" name="_method" value="PATCH">
								<input type="hidden" name="status" value="<?php echo $status; ?>">
								<button type="submit" class="rounded border border-slate-300 px-3 py-1.5 text-xs hover:bg-slate-50">
									<?php echo ucfirst($status); ?>
								</button>
							</form>
						<?php endforeach; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
