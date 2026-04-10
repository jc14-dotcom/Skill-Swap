<?php declare(strict_types=1); ?>

<h1 class="mb-4 text-2xl font-semibold">Dashboard</h1>

<div class="grid gap-4 md:grid-cols-3">
	<div class="rounded border border-slate-200 bg-white p-4">
		<p class="text-sm text-slate-500">Public Services</p>
		<p class="text-2xl font-bold"><?php echo count($publicServices ?? []); ?></p>
	</div>

	<div class="rounded border border-slate-200 bg-white p-4">
		<p class="text-sm text-slate-500">My Seeker Bookings</p>
		<p class="text-2xl font-bold"><?php echo count($myBookings ?? []); ?></p>
	</div>

	<div class="rounded border border-slate-200 bg-white p-4">
		<p class="text-sm text-slate-500">My Provider Bookings</p>
		<p class="text-2xl font-bold"><?php echo count($providerBookings ?? []); ?></p>
	</div>
</div>
