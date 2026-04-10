<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">Create Booking</h1>

<form action="/bookings" method="post" class="max-w-2xl space-y-4 rounded border border-slate-200 bg-white p-5">
	<?php echo csrf_field(); ?>

	<div>
		<label for="service_id" class="mb-1 block text-sm font-medium">Service ID</label>
		<?php $serviceId = is_array($service ?? null) ? (int) $service['id'] : (int) old_input('service_id', 0); ?>
		<input id="service_id" name="service_id" type="number" min="1" required
			   value="<?php echo $serviceId > 0 ? $serviceId : ''; ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
		<?php if (is_array($service ?? null)): ?>
			<p class="mt-1 text-xs text-slate-500">
				Booking for: <?php echo htmlspecialchars((string) $service['title'], ENT_QUOTES, 'UTF-8'); ?>
			</p>
		<?php endif; ?>
	</div>

	<div>
		<label for="scheduled_at" class="mb-1 block text-sm font-medium">Scheduled At (optional)</label>
		<input id="scheduled_at" name="scheduled_at" type="datetime-local"
			   value="<?php echo htmlspecialchars((string) old_input('scheduled_at'), ENT_QUOTES, 'UTF-8'); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="message" class="mb-1 block text-sm font-medium">Message to Provider (optional)</label>
		<textarea id="message" name="message" rows="4" class="w-full rounded border border-slate-300 px-3 py-2"><?php echo htmlspecialchars((string) old_input('message'), ENT_QUOTES, 'UTF-8'); ?></textarea>
	</div>

	<button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white">Submit Booking</button>
</form>
