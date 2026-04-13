<?php
declare(strict_types=1);

$pageTitle = htmlspecialchars((string) ($title ?? 'SkillSwap'), ENT_QUOTES, 'UTF-8');
$allErrors = form_errors();
$success = session_get('success');
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

	<main class="ss-auth-main">
		<div class="ss-shell">
			<?php if (is_string($success) && $success !== ''): ?>
				<div class="ss-alert-stack">
					<div class="ss-alert ss-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
				</div>
			<?php endif; ?>

			<?php if (is_array($allErrors) && $allErrors !== []): ?>
				<div class="ss-alert-stack">
					<div class="ss-alert ss-alert-error">Please review your form input and try again.</div>
				</div>
			<?php endif; ?>

			<div class="ss-auth-card">
				<div class="ss-auth-side">
					<div>
						<span class="ss-section-label">SkillSwap Access</span>
						<h2>Build Trust Through Shared Skills.</h2>
						<p>Join a community where providers and seekers collaborate, book confidently, and grow together.</p>
					</div>

					<div class="ss-auth-badge">
						Secure sessions, CSRF protection, and policy-based workflows are active by default.
					</div>
				</div>

				<div class="ss-auth-form-wrap">
					<?php echo $content ?? ''; ?>
				</div>
			</div>
		</div>
	</main>

	<?php include __DIR__ . '/footer.php'; ?>
	<button type="button" class="ss-floating-top" aria-label="Back to top" data-back-to-top>^</button>
	<script src="/assets/js/app.js?v=<?php echo urlencode($appJsVersion); ?>"></script>
</body>
</html>
