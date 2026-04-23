<?php declare(strict_types=1); ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
			<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
				<span class="h-px w-8 bg-brand-300"></span>
				Provider Portal
			</span>
			<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Create A New Skill Service</h1>
			<p class="mt-3 text-sm leading-7 text-neutral-700">Write clear deliverables and pricing so seekers can book with confidence.</p>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
			<form action="/services" method="post" class="space-y-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8">
				<?php echo csrf_field(); ?>

				<div class="grid gap-2" data-field>
					<label for="title" class="text-sm font-extrabold text-neutral-800">Service title</label>
					<input id="title" name="title" type="text" required value="<?php echo htmlspecialchars((string) old_input('title'), ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
				</div>

				<div class="grid gap-2" data-field>
					<label for="description" class="text-sm font-extrabold text-neutral-800">Description</label>
					<textarea id="description" name="description" rows="6" required class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm leading-7 text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><?php echo htmlspecialchars((string) old_input('description'), ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="grid gap-2" data-field>
					<label for="price" class="text-sm font-extrabold text-neutral-800">Price</label>
					<input id="price" name="price" type="number" min="1" step="0.01" required value="<?php echo htmlspecialchars((string) old_input('price'), ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
				</div>

				<div class="flex flex-wrap gap-3 pt-1">
					<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Save Service <span aria-hidden="true">-></span></button>
					<a href="/services/mine" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">Cancel</a>
				</div>
			</form>

			<aside class="space-y-4">
				<section class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
					<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Provider Focus</span>
					<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Position your service for faster booking.</h2>
					<p class="mt-2 text-sm leading-7 text-neutral-700">Strong service listings reduce back-and-forth messages and increase high-quality booking requests.</p>
					<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
						<li>Use a title that states your exact offer.</li>
						<li>Outline clear deliverables and revision limits.</li>
						<li>Set a price that matches scope and turnaround.</li>
					</ul>
				</section>

				<section class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft">
					<h3 class="text-base font-heading font-extrabold text-neutral-950">Listing Quality</h3>
					<div class="mt-3 space-y-2 text-sm">
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Clear title</span><strong class="font-extrabold text-neutral-900">High impact</strong></div>
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Detailed scope</span><strong class="font-extrabold text-neutral-900">Builds trust</strong></div>
						<div class="flex items-center justify-between gap-3"><span class="text-neutral-600">Accurate pricing</span><strong class="font-extrabold text-neutral-900">Better matches</strong></div>
					</div>
				</section>
			</aside>
		</div>
	</div>
</section>
