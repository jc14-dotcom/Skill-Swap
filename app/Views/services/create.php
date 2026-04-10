<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">Create Service</h1>

<form action="/services" method="post" class="max-w-2xl space-y-4 rounded border border-slate-200 bg-white p-5">
	<?php echo csrf_field(); ?>

	<div>
		<label for="title" class="mb-1 block text-sm font-medium">Title</label>
		<input id="title" name="title" type="text" required
			   value="<?php echo htmlspecialchars((string) old_input('title'), ENT_QUOTES, 'UTF-8'); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<div>
		<label for="description" class="mb-1 block text-sm font-medium">Description</label>
		<textarea id="description" name="description" rows="5" required class="w-full rounded border border-slate-300 px-3 py-2"><?php echo htmlspecialchars((string) old_input('description'), ENT_QUOTES, 'UTF-8'); ?></textarea>
	</div>

	<div>
		<label for="price" class="mb-1 block text-sm font-medium">Price</label>
		<input id="price" name="price" type="number" min="1" step="0.01" required
			   value="<?php echo htmlspecialchars((string) old_input('price'), ENT_QUOTES, 'UTF-8'); ?>"
			   class="w-full rounded border border-slate-300 px-3 py-2">
	</div>

	<button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white">Save Service</button>
</form>
