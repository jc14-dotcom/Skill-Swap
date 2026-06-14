<?php
declare(strict_types=1);

$pageTitle = e($title ?? 'SkillSwap');
$allErrors = form_errors();
$success = session_get('success');
$appCssPath = __DIR__ . '/../../../public/assets/css/app.css';
$appCssVersion = is_file($appCssPath) ? (string) filemtime($appCssPath) : '1';
$appJsPath = __DIR__ . '/../../../public/assets/js/app.js';
$appJsVersion = is_file($appJsPath) ? (string) filemtime($appJsPath) : '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $pageTitle; ?></title>
	<!-- Favicon -->
	<link rel="icon" type="image/png" sizes="any" href="/assets/images/skillswap-logo.png">
	<link rel="shortcut icon" href="/assets/images/skillswap-logo.png">
	<link rel="apple-touch-icon" href="/assets/images/skillswap-logo.png">
	<meta name="theme-color" content="#7a0000">
	<link rel="preload" href="/assets/fonts/manrope-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="/assets/fonts/manrope-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="/assets/fonts/sora-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="stylesheet" href="/assets/css/app.css?v=<?php echo urlencode($appCssVersion); ?>">
</head>
<body class="min-h-screen bg-surface-page font-sans text-neutral-900 antialiased">
	<?php include __DIR__ . '/navbar.php'; ?>

	<main class="py-10">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
			<?php if (is_string($success) && $success !== ''): ?>
				<div class="mb-4">
					<div class="rounded-2xl border border-success-300 bg-success-50 px-4 py-3 text-sm font-semibold text-success-800 shadow-soft"><?php echo e($success); ?></div>
				</div>
			<?php endif; ?>

			<?php if ((!isset($suppressGlobalErrors) || !$suppressGlobalErrors) && is_array($allErrors) && $allErrors !== []): ?>
				<div class="mb-4">
					<div class="rounded-2xl border border-danger-300 bg-danger-50 px-4 py-3 text-sm text-danger-800 shadow-soft">
						<p class="font-extrabold">Please fix the following issues:</p>
						<ul class="mt-2 list-disc space-y-1 pl-5">
							<?php foreach ($allErrors as $fieldErrors): ?>
								<?php foreach ((array) $fieldErrors as $message): ?>
									<li><?php echo e($message); ?></li>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>

			<div class="grid overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-card lg:grid-cols-[1fr,1.1fr]">
				<div class="relative flex flex-col justify-between gap-6 bg-gradient-to-br from-brand-700 via-brand-600 to-brandAccent-tintSoft p-8 text-white">
					<div aria-hidden="true" class="pointer-events-none absolute -right-14 -top-16 h-48 w-48 rounded-full border border-overlay-white35"></div>
					<div aria-hidden="true" class="pointer-events-none absolute -bottom-14 -left-14 h-44 w-44 rounded-full bg-overlay-white18"></div>

					<div>
						<span class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.18em] text-brand-100"><span class="h-px w-8 bg-overlay-white35"></span>SkillSwap Access</span>
						<h2 class="mt-3 max-w-[12ch] text-4xl font-heading font-extrabold leading-tight text-white">Build Trust Through Shared Skills.</h2>
						<p class="mt-3 max-w-[34ch] text-sm leading-7 text-brand-100">Join a community where providers and seekers collaborate, book confidently, and grow together.</p>
					</div>

					<div class="max-w-md rounded-2xl border border-overlay-white35 bg-white/90 px-4 py-3 text-sm font-bold leading-6 text-brand-700">
						Secure sessions, CSRF protection, and policy-based workflows are active by default.
					</div>
				</div>

				<div class="p-8 lg:p-10">
					<?php echo $content ?? ''; ?>
				</div>
			</div>
		</div>
	</main>

	<?php include __DIR__ . '/footer.php'; ?>
	<button type="button" class="fixed bottom-6 right-6 z-50 inline-flex h-11 w-11 translate-y-2 items-center justify-center rounded-full bg-brand-500 text-lg font-black text-white opacity-0 shadow-brand transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brandSoft pointer-events-none" aria-label="Back to top" data-back-to-top>
		<span aria-hidden="true">^</span>
	</button>
	<script src="/assets/js/app.js?v=<?php echo urlencode($appJsVersion); ?>"></script>
</body>
</html>
