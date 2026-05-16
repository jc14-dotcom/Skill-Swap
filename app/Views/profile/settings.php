<?php declare(strict_types=1); ?>

<?php $profileData = is_array($profile ?? null) ? $profile : []; ?>
<?php $profileImageUrl = profile_image_url((string) ($profileData['profile_picture'] ?? '')); ?>
<?php $defaultProfileImageUrl = profile_image_url(''); ?>
<?php $hasCustomProfileImage = $profileImageUrl !== $defaultProfileImageUrl; ?>

<section class="pt-4 pb-14 md:pt-6 md:pb-16">
	<div class="mx-auto w-[min(1320px,calc(100%-2rem))] space-y-8">
		<header class="flex flex-col gap-5 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8 lg:flex-row lg:items-end lg:justify-between">
			<div>
				<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					Account
				</span>
				<h1 class="mt-3 text-3xl font-heading font-extrabold text-neutral-950 md:text-4xl">Profile Settings</h1>
				<p class="mt-3 text-sm leading-7 text-neutral-700">Manage your profile details used across SkillSwap.</p>
			</div>

			<div class="flex flex-wrap gap-2" aria-hidden="true">
				<span class="rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Secure Profile</span>
				<span class="rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Live Session</span>
			</div>
		</header>

		<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),22rem]">
			<form
				id="profile-settings-form"
				action="/profile/settings"
				method="post"
				enctype="multipart/form-data"
				class="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-soft md:p-8"
				data-submit-once
				data-submit-requires-dirty="true"
				data-original-name="<?php echo htmlspecialchars(trim((string) ($profileData['name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>"
				data-original-email="<?php echo htmlspecialchars(strtolower(trim((string) ($profileData['email'] ?? ''))), ENT_QUOTES, 'UTF-8'); ?>"
			>
				<?php echo csrf_field(); ?>

				<div class="rounded-3xl border border-neutral-200 bg-surface-page p-5" data-profile-photo-shell data-has-custom-photo="<?php echo $hasCustomProfileImage ? '1' : '0'; ?>">
					<div class="flex flex-col gap-5 md:flex-row md:items-start">
						<div class="flex flex-col items-center gap-3">
							<button type="button" class="group relative overflow-hidden rounded-3xl border border-neutral-200 bg-white p-1 transition hover:border-brand-400" data-profile-photo-trigger aria-label="Choose a profile picture">
								<img
									src="<?php echo htmlspecialchars($profileImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
									alt="Profile picture"
									class="h-32 w-32 rounded-[1.15rem] object-cover"
									data-profile-photo-preview
									data-default-src="<?php echo htmlspecialchars($defaultProfileImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
									data-fallback-src="<?php echo htmlspecialchars($defaultProfileImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
								>
							</button>

							<button type="button" class="inline-flex items-center gap-2 rounded-full border border-danger-300 bg-white px-4 py-2 text-xs font-extrabold text-danger-700 transition hover:bg-danger-50" data-profile-photo-remove>
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" aria-hidden="true">
									<path d="M4.5 7.5H19.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
									<path d="M9.75 10.5V16.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
									<path d="M14.25 10.5V16.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
									<path d="M7.5 7.5V6C7.5 5.17 8.17 4.5 9 4.5H15C15.83 4.5 16.5 5.17 16.5 6V7.5" stroke="currentColor" stroke-width="1.6"/>
									<path d="M6.75 7.5V17.25C6.75 18.08 7.42 18.75 8.25 18.75H15.75C16.58 18.75 17.25 18.08 17.25 17.25V7.5" stroke="currentColor" stroke-width="1.6"/>
								</svg>
								<span>Remove</span>
							</button>
						</div>

						<div class="grid flex-1 gap-4">
							<div class="grid gap-4 md:grid-cols-2 md:items-start">
								<div class="grid gap-2" data-field>
									<label for="name" class="flex items-center gap-2 text-sm font-extrabold text-neutral-800">
										<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 text-brand-700">
											<circle cx="12" cy="8.25" r="3.25" stroke="currentColor" stroke-width="1.6"/>
											<path d="M5.25 18C5.25 15.38 8.27 13.5 12 13.5C15.73 13.5 18.75 15.38 18.75 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
										</svg>
										<span>Display Name</span>
									</label>
									<input id="name" name="name" type="text" required maxlength="120" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100" value="<?php echo htmlspecialchars((string) old_input('name', (string) ($profileData['name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
								</div>

								<div class="grid gap-2" data-field>
									<label for="email" class="flex items-center gap-2 text-sm font-extrabold text-neutral-800">
										<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 text-brand-700">
											<rect x="3.75" y="5.25" width="16.5" height="13.5" rx="1.8" stroke="currentColor" stroke-width="1.6"/>
											<path d="M4.5 6L12 11.25L19.5 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<span>Email Address</span>
									</label>
									<input id="email" name="email" type="email" required maxlength="190" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100" value="<?php echo htmlspecialchars((string) old_input('email', (string) ($profileData['email'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
								</div>
							</div>

							<p class="flex items-start gap-2 text-xs leading-6 text-neutral-600">
								<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="mt-0.5 h-4 w-4 flex-none text-brand-700">
									<path d="M8.25 6.75L9.3 5.25H14.7L15.75 6.75H18.75C19.58 6.75 20.25 7.42 20.25 8.25V17.25C20.25 18.08 19.58 18.75 18.75 18.75H5.25C4.42 18.75 3.75 18.08 3.75 17.25V8.25C3.75 7.42 4.42 6.75 5.25 6.75H8.25Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
									<circle cx="12" cy="12.75" r="3" stroke="currentColor" stroke-width="1.6"/>
								</svg>
								<span>Click the photo to upload a new one, or remove it to use the default avatar.</span>
							</p>
						</div>
					</div>

					<input id="profile_picture" name="profile_picture" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="sr-only" data-profile-picture-input data-file-input-hidden>
					<input type="hidden" name="remove_profile_picture" value="0" data-remove-profile-picture>
				</div>

				<div class="grid gap-4 md:grid-cols-2 md:items-start">
					<div class="grid gap-2 md:col-span-2" data-field>
						<label for="role" class="flex items-center gap-2 text-sm font-extrabold text-neutral-800">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 text-brand-700">
								<path d="M12 3.75L14.06 7.92L18.66 8.59L15.33 11.84L16.12 16.41L12 14.25L7.88 16.41L8.67 11.84L5.34 8.59L9.94 7.92L12 3.75Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
							</svg>
							<span>Role</span>
						</label>
						<input id="role" type="text" class="w-full rounded-2xl border border-neutral-300 bg-neutral-100 px-4 py-3 text-sm font-semibold uppercase text-neutral-600" value="<?php echo htmlspecialchars(strtoupper((string) ($profileData['role'] ?? 'member')), ENT_QUOTES, 'UTF-8'); ?>" disabled>
					</div>

					<div class="grid gap-2" data-field>
						<label for="password" class="flex items-center gap-2 text-sm font-extrabold text-neutral-800">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 text-brand-700">
								<rect x="4.5" y="10.5" width="15" height="9" rx="2" stroke="currentColor" stroke-width="1.6"/>
								<path d="M8.25 10.5V8.25C8.25 6.18 9.93 4.5 12 4.5C14.07 4.5 15.75 6.18 15.75 8.25V10.5" stroke="currentColor" stroke-width="1.6"/>
							</svg>
							<span>New Password (optional)</span>
						</label>
						<input id="password" name="password" type="password" minlength="8" maxlength="128" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Leave blank to keep current password">
					</div>

					<div class="grid gap-2" data-field>
						<label for="password_confirmation" class="flex items-center gap-2 text-sm font-extrabold text-neutral-800">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 text-brand-700">
								<path d="M7.5 12.75L10.5 15.75L16.5 9.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
								<rect x="4.5" y="4.5" width="15" height="15" rx="2" stroke="currentColor" stroke-width="1.6"/>
							</svg>
							<span>Confirm New Password</span>
						</label>
						<input id="password_confirmation" name="password_confirmation" type="password" minlength="8" maxlength="128" class="w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
					</div>
				</div>

				<div class="flex flex-wrap gap-3">
					<button type="submit" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600 disabled:cursor-not-allowed disabled:bg-neutral-300 disabled:text-neutral-600 disabled:shadow-soft disabled:hover:translate-y-0 disabled:hover:bg-neutral-300" data-submit-once-button data-loading-text="Saving..." disabled>
						<span class="inline-flex h-4 w-4" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
								<path d="M5.25 4.5H16.5L18.75 6.75V19.5H5.25V4.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
								<path d="M8.25 4.5V10.5H15V4.5" stroke="currentColor" stroke-width="1.6"/>
								<path d="M8.25 19.5V14.25H15.75V19.5" stroke="currentColor" stroke-width="1.6"/>
							</svg>
						</span>
						<span>Save Changes</span>
					</button>
					<a href="/home" class="inline-flex items-center gap-2 rounded-full border border-neutral-300 bg-white px-5 py-2.5 text-sm font-extrabold text-neutral-900 transition hover:-translate-y-0.5 hover:border-brand-500 hover:text-brand-700">
						<span class="inline-flex h-4 w-4" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4">
								<path d="M9.75 6.75L4.5 12L9.75 17.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M4.5 12H19.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
							</svg>
						</span>
						<span>Back to Home</span>
					</a>
				</div>
			</form>

			<aside class="rounded-3xl border border-brand-200 bg-surface-page p-6 shadow-soft">
				<span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-extrabold uppercase tracking-wide text-brand-700">Profile Accent</span>
				<h2 class="mt-3 text-xl font-heading font-extrabold text-neutral-950">Keep your account details current.</h2>
				<p class="mt-2 text-sm leading-7 text-neutral-700">Accurate profile details make collaboration smoother and help members identify the right booking counterpart.</p>
				<ul class="mt-4 grid gap-2 text-sm text-neutral-700">
					<li>Use a clear display name for better trust</li>
					<li>Keep your email updated for account recovery</li>
					<li>Rotate your password regularly for security</li>
				</ul>
			</aside>
		</div>
	</div>
</section>
