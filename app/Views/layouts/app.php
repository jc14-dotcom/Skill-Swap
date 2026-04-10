<?php
declare(strict_types=1);

$pageTitle = htmlspecialchars((string) ($title ?? 'SkillSwap'), ENT_QUOTES, 'UTF-8');
$success = session_get('success');
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
<body class="min-h-screen bg-slate-100 text-slate-900">
	<?php include __DIR__ . '/navbar.php'; ?>

	<main class="mx-auto max-w-6xl px-4 py-6">
		<?php if (is_string($success) && $success !== ''): ?>
			<div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
				<?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
			</div>
		<?php endif; ?>

		<?php if (is_array($allErrors) && $allErrors !== []): ?>
			<div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-rose-800">
				<p class="font-semibold">Please fix the following issues:</p>
				<ul class="ml-5 list-disc text-sm">
					<?php foreach ($allErrors as $fieldErrors): ?>
						<?php foreach ((array) $fieldErrors as $message): ?>
							<li><?php echo htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8'); ?></li>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php echo $content ?? ''; ?>
	</main>

	<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
