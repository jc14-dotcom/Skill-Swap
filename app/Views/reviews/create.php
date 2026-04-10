<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">Submit Review</h1>

<form action="/reviews" method="post" class="max-w-2xl space-y-4 rounded border border-slate-200 bg-white p-5">
	<?php echo csrf_field(); ?>

	<div>
		<label for="booking_id" class="mb-1 block text-sm font-medium">Booking ID</label>
		<input id="booking_id" name="booking_id" type="number" min="1" required
			   value="<?php echo (int) old_input('booking_id', (int) ($bookingId ?? 0)); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="rating" class="mb-1 block text-sm font-medium">Rating</label>
		<?php $oldRating = (string) old_input('rating', '5'); ?>
		<select id="rating" name="rating" class="w-full rounded border border-slate-300 px-3 py-2">
			<?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
				<option value="<?php echo $rating; ?>" <?php echo $oldRating === (string) $rating ? 'selected' : ''; ?>>
					<?php echo $rating; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div>
		<label for="comment" class="mb-1 block text-sm font-medium">Comment (optional)</label>
		<textarea id="comment" name="comment" rows="4" class="w-full rounded border border-slate-300 px-3 py-2"><?php echo htmlspecialchars((string) old_input('comment'), ENT_QUOTES, 'UTF-8'); ?></textarea>
	</div>

	<button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white">Submit Review</button>
</form>
