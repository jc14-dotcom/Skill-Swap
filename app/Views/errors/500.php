<?php
declare(strict_types=1);

$safeMessage = htmlspecialchars((string) ($errorMessage ?? 'Unexpected server error.'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>500 Server Error</title>
	<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100">
	<div class="max-w-lg rounded border border-slate-200 bg-white p-8 text-center">
		<h1 class="text-2xl font-bold text-slate-900">500</h1>
		<p class="mt-2 text-slate-600">A server error occurred.</p>
		<p class="mt-3 rounded bg-slate-100 px-3 py-2 text-left text-xs text-slate-500"><?php echo $safeMessage; ?></p>
		<a href="/services" class="mt-4 inline-block rounded bg-slate-900 px-4 py-2 text-white">Back to Services</a>
	</div>
</body>
</html>
