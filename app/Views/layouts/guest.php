<?php
declare(strict_types=1);

$pageTitle = htmlspecialchars((string) ($title ?? 'SkillSwap'), ENT_QUOTES, 'UTF-8');
$allErrors = form_errors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $pageTitle; ?></title>
	<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4">
	<div class="w-full max-w-md rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
		<h1 class="mb-4 text-2xl font-semibold text-slate-900">SkillSwap</h1>

		<?php if (is_array($allErrors) && $allErrors !== []): ?>
			<div class="mb-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
				Please review your form input.
			</div>
		<?php endif; ?>

		<?php echo $content ?? ''; ?>
	</div>
</body>
</html>
