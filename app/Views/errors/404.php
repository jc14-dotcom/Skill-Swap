<?php
declare(strict_types=1);

$appCssPath = __DIR__ . '/../../../public/assets/css/app.css';
$appCssVersion = is_file($appCssPath) ? (string) filemtime($appCssPath) : '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>404 Not Found</title>
	<link rel="preload" href="/assets/fonts/manrope-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="preload" href="/assets/fonts/sora-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>
	<link rel="stylesheet" href="/assets/css/app.css?v=<?php echo urlencode($appCssVersion); ?>">
</head>
<body class="min-h-screen bg-surface-page font-sans text-neutral-900 antialiased">
	<main class="py-10">
		<div class="mx-auto w-[min(1320px,calc(100%-2rem))]">
			<div class="grid min-h-[24rem] place-content-center rounded-3xl border border-neutral-200 bg-white p-8 text-center shadow-card">
				<span class="inline-flex items-center justify-center gap-3 text-xs font-extrabold uppercase tracking-[0.16em] text-brand-700">
					<span class="h-px w-8 bg-brand-300"></span>
					404 Error
					<span class="h-px w-8 bg-brand-300"></span>
				</span>
				<h1 class="mt-4 text-4xl font-heading font-extrabold text-neutral-950 md:text-5xl">Page not found</h1>
				<p class="mt-3 text-sm leading-7 text-neutral-700">The page you requested could not be found.</p>
				<div class="mt-5 flex justify-center">
					<a href="/services" class="inline-flex items-center gap-2 rounded-full bg-brand-500 px-5 py-2.5 text-sm font-extrabold text-white shadow-brand transition hover:-translate-y-0.5 hover:bg-brand-600">Back to Services <span aria-hidden="true">-></span></a>
				</div>
			</div>
		</div>
	</main>
</body>
</html>
