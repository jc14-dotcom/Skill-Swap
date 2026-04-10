<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">My Bookings</h1>

<?php if (($bookings ?? []) === []): ?>
	<p class="text-slate-600">No bookings yet.</p>
<?php else: ?>
	<div class="overflow-x-auto rounded border border-slate-200 bg-white">
		<table class="min-w-full text-sm">
			<thead class="bg-slate-50 text-left">
				<tr>
					<th class="px-3 py-2">Service</th>
					<th class="px-3 py-2">Provider</th>
					<th class="px-3 py-2">Status</th>
					<th class="px-3 py-2">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($bookings as $booking): ?>
					<tr class="border-t border-slate-100">
						<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="px-3 py-2"><?php echo htmlspecialchars((string) ($booking['provider_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></td>
						<td class="px-3 py-2">
							<?php if ((string) $booking['status'] === 'completed'): ?>
								<a class="underline" href="/reviews/create?booking_id=<?php echo (int) $booking['id']; ?>">Add review</a>
							<?php else: ?>
								-
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
