<?php
declare(strict_types=1);

$pageTitle = htmlspecialchars((string) ($title ?? 'SkillSwap'), ENT_QUOTES, 'UTF-8');
$success = session_get('success');
$allErrors = form_errors();
$appJsPath = __DIR__ . '/../../../public/assets/js/app.js';
$appJsVersion = is_file($appJsPath) ? (string) filemtime($appJsPath) : '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $pageTitle; ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="ss-site">
	<?php include __DIR__ . '/navbar.php'; ?>

	<main class="ss-main">
		<div class="ss-shell">
			<?php if (is_string($success) && $success !== ''): ?>
				<div class="ss-alert-stack">
					<div class="ss-alert ss-alert-success">
						<?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if (is_array($allErrors) && $allErrors !== []): ?>
				<div class="ss-alert-stack">
					<div class="ss-alert ss-alert-error">
						<p><strong>Please fix the following issues:</strong></p>
						<ul>
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

	<button type="button" class="ss-floating-top" aria-label="Back to top" data-back-to-top>^</button>
	<script src="/assets/js/app.js?v=<?php echo urlencode($appJsVersion); ?>"></script>
</body>
</html>
