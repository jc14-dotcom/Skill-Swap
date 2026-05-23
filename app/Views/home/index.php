<?php declare(strict_types=1); ?>

<?php
$userName = htmlspecialchars((string) ($user['name'] ?? 'Member'), ENT_QUOTES, 'UTF-8');
$roleLabel = strtoupper(htmlspecialchars((string) ($role ?? 'seeker'), ENT_QUOTES, 'UTF-8'));
$rawRole = strtolower((string) ($role ?? 'seeker'));
$canSeekSkill = $rawRole !== 'provider';
$canOfferSkill = $rawRole !== 'seeker';
?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
        <div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
                <header class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                                <span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
                                        <span class="h-px w-8 bg-brand-700"></span>
                                        Member Home
                                        <span class="h-px w-8 bg-brand-700"></span>
                                </span>
                                <h1 class="mt-4 text-4xl font-heading font-extrabold text-neutral-950 md:text-5xl">Welcome, <?php echo $userName; ?></h1>
                                <p class="mt-3 max-w-[64ch] text-sm leading-7 text-slate-500">You are logged in as <?php echo $roleLabel; ?>. In SkillSwap, every member can seek new skills and also offer their own.</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                                <?php if ($canSeekSkill): ?>
                                        <a href="/services" class="inline-flex items-center gap-2 rounded-full bg-brand-700 px-6 py-3 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-800">Seek a skill <span aria-hidden="true" class="text-brand-200">-></span></a>
                                <?php endif; ?>
                                <?php if ($canOfferSkill): ?>
                                        <a href="/services/create" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-6 py-3 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 hover:bg-brand-50">Offer a skill</a>
                                <?php endif; ?>
                        </div>
                </header>

                <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-[1.2rem] border border-neutral-300 bg-surface-warm p-6 shadow-soft md:p-8">
                                <p class="text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Public Services</p>        
                                <p class="mt-3 text-[2.2rem] font-heading font-extrabold text-brand-950"><?php echo (int) ($publicServiceCount ?? 0); ?></p>
                        </div>

                        <div class="rounded-[1.2rem] border border-neutral-300 bg-surface-warm p-6 shadow-soft md:p-8">
                                <p class="text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">My Seeker Bookings</p>     
                                <p class="mt-3 text-[2.2rem] font-heading font-extrabold text-brand-950"><?php echo (int) ($myBookingCount ?? 0); ?></p>
                        </div>

                        <div class="rounded-[1.2rem] border border-neutral-300 bg-surface-warm p-6 shadow-soft md:p-8">
                                <p class="text-xs font-extrabold uppercase tracking-[0.12em] text-slate-500">Incoming Provider Bookings</p>
                                <p class="mt-3 text-[2.2rem] font-heading font-extrabold text-brand-950"><?php echo (int) ($providerBookingCount ?? 0); ?></p>
                        </div>
                </div>

                <section class="rounded-[1.2rem] border border-neutral-300 bg-surface-warm p-6 shadow-soft md:p-8">
                        <h2 class="text-[1.65rem] font-heading font-extrabold text-neutral-950">Quick Actions</h2>
                        <div class="mt-6 flex flex-wrap gap-3">
                                <?php if ($canSeekSkill): ?>
                                        <a href="/services" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 hover:bg-brand-50">Seek skill</a>
                                        <a href="/bookings/mine" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 hover:bg-brand-50">My bookings</a>
                                <?php endif; ?>

                                <?php if ($canOfferSkill): ?>
                                        <a href="/services/create" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 hover:bg-brand-50">Offer skill</a>
                                        <a href="/services/mine" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 hover:bg-brand-50">My services (<?php echo (int) ($myServiceCount ?? 0); ?>)</a>
                                <?php endif; ?>

                                <?php if ($canOfferSkill): ?>
                                        <a href="/bookings/provider" class="inline-flex items-center rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700 hover:bg-brand-50">Provider schedule</a>
                                <?php endif; ?>
                                <?php if ($canOfferSkill): ?>
                                        <a href="/services/create" class="inline-flex items-center gap-2 rounded-full bg-brand-700 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-800">Create service <span aria-hidden="true" class="text-brand-200">-></span></a>
                                <?php endif; ?>
                        </div>
                </section>
        </div>
</section>
