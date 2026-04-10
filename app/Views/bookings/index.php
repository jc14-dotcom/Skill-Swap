<?php declare(strict_types=1); ?>

<h1 class="mb-6 text-2xl font-semibold">Bookings Overview</h1>

<section class="mb-6">
	<h2 class="mb-2 text-lg font-semibold">As Seeker</h2>
	<?php if (($seekerBookings ?? []) === []): ?>
		<p class="text-sm text-slate-600">No bookings yet.</p>
	<?php else: ?>
		<div class="overflow-x-auto rounded border border-slate-200 bg-white">
			<table class="min-w-full text-sm">
				<thead class="bg-slate-50 text-left">
					<tr>
						<th class="px-3 py-2">Service</th>
						<th class="px-3 py-2">Provider</th>
						<th class="px-3 py-2">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($seekerBookings as $booking): ?>
						<tr class="border-t border-slate-100">
							<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['provider_name'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>

<section>
	<h2 class="mb-2 text-lg font-semibold">As Provider</h2>
	<?php if (($providerBookings ?? []) === []): ?>
		<p class="text-sm text-slate-600">No incoming booking requests.</p>
	<?php else: ?>
		<div class="overflow-x-auto rounded border border-slate-200 bg-white">
			<table class="min-w-full text-sm">
				<thead class="bg-slate-50 text-left">
					<tr>
						<th class="px-3 py-2">Service</th>
						<th class="px-3 py-2">Seeker</th>
						<th class="px-3 py-2">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($providerBookings as $booking): ?>
						<tr class="border-t border-slate-100">
							<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['service_title'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['seeker_name'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td class="px-3 py-2"><?php echo htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8'); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</section>
