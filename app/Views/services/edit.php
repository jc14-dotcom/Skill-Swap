<?php declare(strict_types=1); ?>

<?php if (!is_array($service ?? null)): ?>
	<p class="text-slate-600">Service not found.</p>
<?php else: ?>
	<h1 class="mb-4 text-2xl font-semibold">Edit Service</h1>

	<form action="/services/<?php echo (int) $service['id']; ?>" method="post" class="max-w-2xl space-y-4 rounded border border-slate-200 bg-white p-5">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="_method" value="PATCH">

		<div>
			<label for="title" class="mb-1 block text-sm font-medium">Title</label>
			<input id="title" name="title" type="text" required
				   value="<?php echo htmlspecialchars((string) old_input('title', (string) $service['title']), ENT_QUOTES, 'UTF-8'); ?>"
				   class="w-full rounded border border-slate-300 px-3 py-2">
		</div>

		<div>
			<label for="description" class="mb-1 block text-sm font-medium">Description</label>
			<textarea id="description" name="description" rows="5" required class="w-full rounded border border-slate-300 px-3 py-2"><?php echo htmlspecialchars((string) old_input('description', (string) $service['description']), ENT_QUOTES, 'UTF-8'); ?></textarea>
		</div>

		<div>
			<label for="price" class="mb-1 block text-sm font-medium">Price</label>
			<input id="price" name="price" type="number" min="1" step="0.01" required
				   value="<?php echo htmlspecialchars((string) old_input('price', (string) $service['price']), ENT_QUOTES, 'UTF-8'); ?>"
				   class="w-full rounded border border-slate-300 px-3 py-2">
		</div>

		<div>
			<label for="status" class="mb-1 block text-sm font-medium">Status</label>
			<?php $status = (string) old_input('status', (string) $service['status']); ?>
			<select id="status" name="status" class="w-full rounded border border-slate-300 px-3 py-2">
				<option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
				<option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
			</select>
		</div>

		<div class="flex flex-wrap gap-3">
			<button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white">Update Service</button>
			<a href="/services/<?php echo (int) $service['id']; ?>" class="rounded border border-slate-300 px-4 py-2">Cancel</a>
		</div>
	</form>

	<form action="/services/<?php echo (int) $service['id']; ?>" method="post" class="mt-4" onsubmit="return confirm('Delete this service? This action cannot be undone.');">
		<?php echo csrf_field(); ?>
		<input type="hidden" name="_method" value="DELETE">
		<button type="submit" class="rounded border border-rose-300 bg-rose-50 px-4 py-2 text-rose-700">Delete Service</button>
	</form>
<?php endif; ?>
