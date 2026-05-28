<?php
declare(strict_types=1);

$pageTitle = htmlspecialchars((string) ($title ?? 'SkillSwap'), ENT_QUOTES, 'UTF-8');
$allErrors = form_errors();
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
<body class="flex min-h-screen flex-col bg-surface-page font-sans text-neutral-900 antialiased">
	<?php include __DIR__ . '/navbar.php'; ?>

	<main class="flex flex-1 flex-col py-6">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
			<?php if (is_array($allErrors) && $allErrors !== []): ?>
				<div class="mb-4">
					<div class="rounded-2xl border border-danger-300 bg-danger-50 px-4 py-3 text-sm text-danger-800 shadow-soft">
						<p class="font-extrabold">Please fix the following issues:</p>
						<ul class="mt-2 list-disc space-y-1 pl-5">
							<?php foreach ($allErrors as $fieldErrors): ?>
								<?php foreach ((array) $fieldErrors as $message): ?>
									<li><?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></li>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<?php echo $content ?? ''; ?>
	</main>

	<?php include __DIR__ . '/footer.php'; ?>

	<button type="button" class="fixed bottom-6 right-6 z-50 inline-flex h-11 w-11 translate-y-2 items-center justify-center rounded-full bg-brand-500 text-lg font-black text-white opacity-0 shadow-brand transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brandSoft pointer-events-none" aria-label="Back to top" data-back-to-top>
		<span aria-hidden="true">^</span>
	</button>
	<script src="/assets/js/app.js?v=<?php echo urlencode($appJsVersion); ?>"></script>
</body>
</html>
