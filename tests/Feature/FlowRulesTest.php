<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));

/**
 * Minimal HTTP client with cookie persistence for request-level feature tests.
 */
final class FlowHttpClient
{
    private string $baseUrl;

    /** @var array<string, string> */
    private array $cookies = [];

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function request(string $method, string $path, array $payload = []): array
    {
        $method = strtoupper($method);
        $url = $this->baseUrl . $path;

        $options = [
            'http' => [
                'method' => $method,
                'ignore_errors' => true,
                'max_redirects' => 0,
                'timeout' => 20,
                'header' => '',
            ],
        ];

        $headers = [];
        if ($this->cookies !== []) {
            $cookieParts = [];
            foreach ($this->cookies as $name => $value) {
                $cookieParts[] = $name . '=' . $value;
            }
            $headers[] = 'Cookie: ' . implode('; ', $cookieParts);
        }

        if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH' || $method === 'DELETE') {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $options['http']['content'] = http_build_query($payload);
        }

        $options['http']['header'] = implode("\r\n", $headers);

        $context = stream_context_create($options);
        $body = @file_get_contents($url, false, $context);
        $responseHeaders = $http_response_header ?? [];

        $this->captureCookies($responseHeaders);

        $status = 0;
        if (isset($responseHeaders[0]) && preg_match('/\s(\d{3})\s/', (string) $responseHeaders[0], $match) === 1) {
            $status = (int) $match[1];
        }

        return [
            'status' => $status,
            'headers' => $responseHeaders,
            'body' => is_string($body) ? $body : '',
            'location' => $this->headerValue($responseHeaders, 'Location'),
        ];
    }

    public function csrfTokenFromBody(string $body): string
    {
        if (preg_match('/name="_token"\s+value="([^"]+)"/', $body, $match) === 1) {
            return html_entity_decode((string) $match[1], ENT_QUOTES, 'UTF-8');
        }

        return '';
    }

    public function resetTokenFromBody(string $body): string
    {
        if (preg_match('/\/reset-password\?token=([a-zA-Z0-9%]+)/', $body, $match) === 1) {
            return urldecode((string) $match[1]);
        }

        return '';
    }

    /** @param array<int, string> $headers */
    private function headerValue(array $headers, string $name): string
    {
        $needle = strtolower($name) . ':';
        foreach ($headers as $header) {
            $line = strtolower($header);
            if (!str_starts_with($line, $needle)) {
                continue;
            }

            return trim(substr($header, strlen($name) + 1));
        }

        return '';
    }

    /** @param array<int, string> $headers */
    private function captureCookies(array $headers): void
    {
        foreach ($headers as $header) {
            if (stripos($header, 'Set-Cookie:') !== 0) {
                continue;
            }

            $cookieLine = trim(substr($header, strlen('Set-Cookie:')));
            $pair = explode(';', $cookieLine, 2)[0] ?? '';
            if (!str_contains($pair, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $pair, 2);
            $this->cookies[trim($name)] = trim($value);
        }
    }
}

loadEnvironment();
$pdo = createDatabaseConnection();

// Clear log file before test to prevent stale tokens from previous runs
$logDir = BASE_PATH . '/storage/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
$logPath = $logDir . '/app.log';
if (is_file($logPath)) {
    file_put_contents($logPath, '');
}

[$serverProcess, $baseUrl] = startServer();
$failures = [];

$assert = static function (bool $condition, string $message) use (&$failures): void {
    if (!$condition) {
        $failures[] = $message;
    }
};

try {
    $client = new FlowHttpClient($baseUrl);
    $stampBase = time() . '_' . random_int(1000, 9999);
    $providerStamp = $stampBase . '_p';
    $seekerStamp = $stampBase . '_s';
    $adminStamp = $stampBase . '_a';
    $providerEmail = 'provider.' . $providerStamp . '@example.com';
    $seekerEmail = 'seeker.' . $seekerStamp . '@example.com';
    $adminEmail = 'admin.' . $adminStamp . '@example.com';
    $initialPassword = 'Password123!';
    $newPassword = 'Password456!';

    $adminInsert = $pdo->prepare(
        'INSERT INTO users (name, email, password, role, created_at, updated_at)
         VALUES (:name, :email, :password, :role, :created_at, :updated_at)'
    );
    $adminInsert->execute([
        'name' => 'Admin ' . $adminStamp,
        'email' => $adminEmail,
        'password' => password_hash($initialPassword, PASSWORD_DEFAULT),
        'role' => 'admin',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $adminId = (int) $pdo->lastInsertId();
    $assert($adminId > 0, 'Admin fixture should be created for access-control verification.');

    $guestCreateService = $client->request('POST', '/services', [
        'title' => 'Unauthorized Service Attempt',
        'description' => 'This should be blocked by auth middleware before controller execution.',
        'price' => '100',
    ]);
    $assert($guestCreateService['status'] === 302, 'Guest service create should redirect to login.');
    $assert($guestCreateService['location'] === '/login', 'Guest service create redirect target should be /login.');

    $registerProviderPage = $client->request('GET', '/register');
    $providerCsrf = $client->csrfTokenFromBody($registerProviderPage['body']);
    $assert($providerCsrf !== '', 'Provider register page must include CSRF token.');

    $registerProvider = $client->request('POST', '/register', [
        '_token' => $providerCsrf,
        'name' => 'Provider ' . $providerStamp,
        'email' => $providerEmail,
        'password' => $initialPassword,
        'password_confirmation' => $initialPassword,
        'role' => 'provider',
    ]);
    $assert($registerProvider['status'] === 302, 'Provider register should redirect after success.');
    $assert($registerProvider['location'] === '/home', 'Provider register redirect should be /home.');

    $serviceCreatePage = $client->request('GET', '/services/create');
    $serviceCsrf = $client->csrfTokenFromBody($serviceCreatePage['body']);
    $assert($serviceCsrf !== '', 'Service create page must include CSRF token.');

    $createService = $client->request('POST', '/services', [
        '_token' => $serviceCsrf,
        'title' => 'Bundle A Test Service ' . $providerStamp,
        'description' => 'Provider test service used for request-level booking transition assertions.',
        'price' => '250',
    ]);
    $assert($createService['status'] === 302, 'Service create should redirect to service page.');
    $assert(preg_match('#^/services/(\d+)$#', $createService['location']) === 1, 'Service create redirect should target /services/{id}.');

    preg_match('#^/services/(\d+)$#', (string) $createService['location'], $serviceMatch);
    $serviceId = (int) ($serviceMatch[1] ?? 0);
    $assert($serviceId > 0, 'Service ID should be parsed from create redirect URL.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $logoutProvider = $client->request('POST', '/logout', ['_token' => $logoutCsrf]);
    $assert($logoutProvider['status'] === 302, 'Provider logout should redirect.');
    $assert($logoutProvider['location'] === '/login', 'Provider logout redirect should be /login.');

    $registerSeekerPage = $client->request('GET', '/register');
    $seekerCsrf = $client->csrfTokenFromBody($registerSeekerPage['body']);
    $registerSeeker = $client->request('POST', '/register', [
        '_token' => $seekerCsrf,
        'name' => 'Seeker ' . $seekerStamp,
        'email' => $seekerEmail,
        'password' => $initialPassword,
        'password_confirmation' => $initialPassword,
        'role' => 'seeker',
    ]);
    $assert($registerSeeker['status'] === 302, 'Seeker register should redirect after success.');
    $assert($registerSeeker['location'] === '/home', 'Seeker register redirect should be /home.');

    $bookingCreatePage = $client->request('GET', '/bookings/create?service_id=' . $serviceId);
    $bookingCsrf = $client->csrfTokenFromBody($bookingCreatePage['body']);
    $createBooking = $client->request('POST', '/bookings', [
        '_token' => $bookingCsrf,
        'service_id' => (string) $serviceId,
        'message' => 'I would like to book this service for automated feature tests.',
    ]);
    $assert($createBooking['status'] === 302, 'Booking create should redirect.');
    $assert($createBooking['location'] === '/bookings/mine', 'Booking create redirect should be /bookings/mine.');

    $bookingRow = fetchOne($pdo, 'SELECT id, status, seeker_id, provider_id FROM bookings WHERE service_id = :service_id ORDER BY id DESC LIMIT 1', [
        'service_id' => $serviceId,
    ]);
    $bookingId = (int) ($bookingRow['id'] ?? 0);
    $assert($bookingId > 0, 'Booking row should exist after create.');
    $assert((string) ($bookingRow['status'] ?? '') === 'pending', 'New booking should start in pending state.');

    $providerBookingNotifCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND type = :type', [
        'user_id' => (int) ($bookingRow['provider_id'] ?? 0),
        'type' => 'booking.created',
    ])['total'] ?? 0);
    $assert($providerBookingNotifCount >= 1, 'Provider should receive booking.created notification after seeker request.');

    $myBookingsPage = $client->request('GET', '/bookings/mine');
    $myBookingsCsrf = $client->csrfTokenFromBody($myBookingsPage['body']);
    $invalidSeekerTransition = $client->request('POST', '/bookings/' . $bookingId . '/status', [
        '_token' => $myBookingsCsrf,
        '_method' => 'PATCH',
        'status' => 'accepted',
    ]);
    $assert($invalidSeekerTransition['status'] === 302, 'Invalid seeker transition should redirect.');
    $assert($invalidSeekerTransition['location'] === '/bookings/mine', 'Invalid seeker transition should redirect back to seeker list.');

    $statusAfterInvalid = (string) (fetchOne($pdo, 'SELECT status FROM bookings WHERE id = :id', ['id' => $bookingId])['status'] ?? '');
    $assert($statusAfterInvalid === 'pending', 'Booking status should remain pending after invalid seeker transition.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $client->request('POST', '/logout', ['_token' => $logoutCsrf]);

    $loginProviderPage = $client->request('GET', '/login');
    $loginProviderCsrf = $client->csrfTokenFromBody($loginProviderPage['body']);
    $loginProvider = $client->request('POST', '/login', [
        '_token' => $loginProviderCsrf,
        'email' => $providerEmail,
        'password' => $initialPassword,
    ]);
    $assert($loginProvider['status'] === 302, 'Provider login should redirect after success.');
    $assert($loginProvider['location'] === '/home', 'Provider login redirect should be /home.');

    $providerQueuePage = $client->request('GET', '/bookings/provider');
    $providerQueueCsrf = $client->csrfTokenFromBody($providerQueuePage['body']);
    $acceptBooking = $client->request('POST', '/bookings/' . $bookingId . '/status', [
        '_token' => $providerQueueCsrf,
        '_method' => 'PATCH',
        'status' => 'accepted',
    ]);
    $assert($acceptBooking['status'] === 302, 'Provider accept booking should redirect.');
    $assert($acceptBooking['location'] === '/bookings/provider', 'Provider accept redirect should be /bookings/provider.');

    $statusAfterAccept = (string) (fetchOne($pdo, 'SELECT status FROM bookings WHERE id = :id', ['id' => $bookingId])['status'] ?? '');
    $assert($statusAfterAccept === 'accepted', 'Booking status should become accepted after provider transition.');

    $seekerAcceptNotifCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND type = :type AND body LIKE :body_like', [
        'user_id' => (int) ($bookingRow['seeker_id'] ?? 0),
        'type' => 'booking.status_changed',
        'body_like' => '%accepted%',
    ])['total'] ?? 0);
    $assert($seekerAcceptNotifCount >= 1, 'Seeker should receive accepted status notification.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $client->request('POST', '/logout', ['_token' => $logoutCsrf]);

    $loginAdminPage = $client->request('GET', '/login');
    $loginAdminCsrf = $client->csrfTokenFromBody($loginAdminPage['body']);
    $loginAdmin = $client->request('POST', '/login', [
        '_token' => $loginAdminCsrf,
        'email' => $adminEmail,
        'password' => $initialPassword,
    ]);
    $assert($loginAdmin['status'] === 302, 'Admin login should redirect after success.');
    $assert($loginAdmin['location'] === '/home', 'Admin login redirect should be /home.');

    $adminThreadPage = $client->request('GET', '/messages/' . $bookingId);
    $assert($adminThreadPage['status'] === 200, 'Admin should be able to open any booking conversation thread.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $client->request('POST', '/logout', ['_token' => $logoutCsrf]);

    $loginSeekerPage = $client->request('GET', '/login');
    $loginSeekerCsrf = $client->csrfTokenFromBody($loginSeekerPage['body']);
    $loginSeeker = $client->request('POST', '/login', [
        '_token' => $loginSeekerCsrf,
        'email' => $seekerEmail,
        'password' => $initialPassword,
    ]);
    $assert($loginSeeker['status'] === 302, 'Seeker login should redirect after success.');

    $messagePage = $client->request('GET', '/messages/' . $bookingId);
    $messageCsrf = $client->csrfTokenFromBody($messagePage['body']);
    $assert($messagePage['status'] === 200, 'Seeker should be able to open booking conversation.');
    $assert($messageCsrf !== '', 'Conversation page should include CSRF token for send action.');

    $sendMessage = $client->request('POST', '/messages/' . $bookingId, [
        '_token' => $messageCsrf,
        'body' => 'Hello provider, can we confirm tomorrow as the schedule?',
    ]);
    $assert($sendMessage['status'] === 302, 'Message send should redirect back to conversation.');
    $assert($sendMessage['location'] === '/messages/' . $bookingId, 'Message send redirect should target booking conversation.');

    $messageCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM messages WHERE booking_id = :booking_id', [
        'booking_id' => $bookingId,
    ])['total'] ?? 0);
    $assert($messageCount >= 1, 'At least one message should be stored after send.');

    $providerMessageNotifCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND type = :type AND link = :link', [
        'user_id' => (int) ($bookingRow['provider_id'] ?? 0),
        'type' => 'message.received',
        'link' => '/messages/' . $bookingId,
    ])['total'] ?? 0);
    $assert($providerMessageNotifCount >= 1, 'Provider should receive message notification after seeker sends a chat message.');

    $reviewFormPage = $client->request('GET', '/reviews/create?booking_id=' . $bookingId);
    $reviewCsrf = $client->csrfTokenFromBody($reviewFormPage['body']);
    $prematureReview = $client->request('POST', '/reviews', [
        '_token' => $reviewCsrf,
        'booking_id' => (string) $bookingId,
        'rating' => '5',
        'comment' => 'This should fail before booking is completed.',
    ]);
    $assert($prematureReview['status'] === 302, 'Premature review attempt should redirect.');

    $prematureCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM reviews WHERE booking_id = :booking_id', ['booking_id' => $bookingId])['total'] ?? 0);
    $assert($prematureCount === 0, 'Review should not be created before booking completion.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $client->request('POST', '/logout', ['_token' => $logoutCsrf]);

    $loginProviderPage = $client->request('GET', '/login');
    $loginProviderCsrf = $client->csrfTokenFromBody($loginProviderPage['body']);
    $client->request('POST', '/login', [
        '_token' => $loginProviderCsrf,
        'email' => $providerEmail,
        'password' => $initialPassword,
    ]);

    $providerUnreadBeforeRead = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM messages WHERE booking_id = :booking_id AND receiver_id = :receiver_id AND read_at IS NULL', [
        'booking_id' => $bookingId,
        'receiver_id' => (int) ($bookingRow['provider_id'] ?? 0),
    ])['total'] ?? 0);
    $assert($providerUnreadBeforeRead >= 1, 'Provider should have unread chat messages before opening the conversation.');

    $providerMessagePage = $client->request('GET', '/messages/' . $bookingId);
    $assert($providerMessagePage['status'] === 200, 'Provider should be able to open booking conversation.');

    $providerUnreadAfterRead = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM messages WHERE booking_id = :booking_id AND receiver_id = :receiver_id AND read_at IS NULL', [
        'booking_id' => $bookingId,
        'receiver_id' => (int) ($bookingRow['provider_id'] ?? 0),
    ])['total'] ?? 0);
    $assert($providerUnreadAfterRead === 0, 'Opening conversation should mark provider unread messages as read.');

    $providerQueuePage = $client->request('GET', '/bookings/provider');
    $providerQueueCsrf = $client->csrfTokenFromBody($providerQueuePage['body']);
    $completeBooking = $client->request('POST', '/bookings/' . $bookingId . '/status', [
        '_token' => $providerQueueCsrf,
        '_method' => 'PATCH',
        'status' => 'completed',
    ]);
    $assert($completeBooking['status'] === 302, 'Provider complete booking should redirect.');

    $statusAfterComplete = (string) (fetchOne($pdo, 'SELECT status FROM bookings WHERE id = :id', ['id' => $bookingId])['status'] ?? '');
    $assert($statusAfterComplete === 'completed', 'Booking status should become completed.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $client->request('POST', '/logout', ['_token' => $logoutCsrf]);

    $loginSeekerPage = $client->request('GET', '/login');
    $loginSeekerCsrf = $client->csrfTokenFromBody($loginSeekerPage['body']);
    $client->request('POST', '/login', [
        '_token' => $loginSeekerCsrf,
        'email' => $seekerEmail,
        'password' => $initialPassword,
    ]);

    $reviewFormPage = $client->request('GET', '/reviews/create?booking_id=' . $bookingId);
    $reviewCsrf = $client->csrfTokenFromBody($reviewFormPage['body']);
    $finalReview = $client->request('POST', '/reviews', [
        '_token' => $reviewCsrf,
        'booking_id' => (string) $bookingId,
        'rating' => '5',
        'comment' => 'Completed booking review from seeker.',
    ]);
    $assert($finalReview['status'] === 302, 'Final review submit should redirect.');

    $reviewCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM reviews WHERE booking_id = :booking_id', ['booking_id' => $bookingId])['total'] ?? 0);
    $assert($reviewCount === 1, 'Review should be created after completion.');

    $providerReviewNotifCount = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND type = :type', [
        'user_id' => (int) ($bookingRow['provider_id'] ?? 0),
        'type' => 'review.created',
    ])['total'] ?? 0);
    $assert($providerReviewNotifCount >= 1, 'Provider should receive review notification after seeker submits review.');

    $csrfRejected = $client->request('POST', '/services', [
        'title' => 'Missing token',
        'description' => 'This request intentionally omits csrf token to test middleware behavior.',
        'price' => '99',
    ]);
    $assert($csrfRejected['status'] === 419, 'State-changing request without csrf token should return 419.');
    $assert(str_contains($csrfRejected['body'], 'CSRF token mismatch'), 'CSRF rejection response should mention mismatch.');

    $homePage = $client->request('GET', '/home');
    $logoutCsrf = $client->csrfTokenFromBody($homePage['body']);
    $client->request('POST', '/logout', ['_token' => $logoutCsrf]);

    $forgotPage = $client->request('GET', '/forgot-password');
    $forgotCsrf = $client->csrfTokenFromBody($forgotPage['body']);
    $forgotSubmit = $client->request('POST', '/forgot-password', [
        '_token' => $forgotCsrf,
        'email' => $seekerEmail,
    ]);
    $assert($forgotSubmit['status'] === 302, 'Forgot password submit should redirect.');
    $assert($forgotSubmit['location'] === '/forgot-password', 'Forgot password submit redirect should be /forgot-password.');

    $resetToken = bin2hex(random_bytes(32));
    $testTokenHash = hash('sha256', $resetToken);
    $testExpiresAt = date('Y-m-d H:i:s', time() + 3600);

    $seekerRow = fetchOne($pdo, 'SELECT id FROM users WHERE email = :email', ['email' => $seekerEmail]);
    $seekerId = (int) ($seekerRow['id'] ?? 0);
    $assert($seekerId > 0, 'Seeker should exist for password reset test.');

    $insertReset = $pdo->prepare(
        'INSERT INTO password_resets (user_id, token_hash, expires_at, created_at, updated_at) VALUES (:uid, :hash, :expires, NOW(), NOW())'
    );
    $insertReset->execute([
        'uid' => $seekerId,
        'hash' => $testTokenHash,
        'expires' => $testExpiresAt,
    ]);

    $resetPage = $client->request('GET', '/reset-password?token=' . urlencode($resetToken));
    $resetCsrf = $client->csrfTokenFromBody($resetPage['body']);
    $assert($resetPage['status'] === 200, 'Reset password page should open with valid token.');
    $assert($resetCsrf !== '', 'Reset password page should include csrf token.');

    $resetSubmit = $client->request('POST', '/reset-password', [
        '_token' => $resetCsrf,
        'token' => $resetToken,
        'password' => $newPassword,
        'password_confirmation' => $newPassword,
    ]);
    $assert($resetSubmit['status'] === 302, 'Reset password submit should redirect after success.');
    $assert($resetSubmit['location'] === '/login', 'Reset password submit redirect should be /login.');

    $oldLoginPage = $client->request('GET', '/login');
    $oldLoginCsrf = $client->csrfTokenFromBody($oldLoginPage['body']);
    $oldLoginAttempt = $client->request('POST', '/login', [
        '_token' => $oldLoginCsrf,
        'email' => $seekerEmail,
        'password' => $initialPassword,
    ]);
    $assert($oldLoginAttempt['status'] === 302, 'Old password attempt should redirect.');
    $assert($oldLoginAttempt['location'] === '/login', 'Old password should no longer authenticate.');

    $newLoginPage = $client->request('GET', '/login');
    $newLoginCsrf = $client->csrfTokenFromBody($newLoginPage['body']);
    $newLoginAttempt = $client->request('POST', '/login', [
        '_token' => $newLoginCsrf,
        'email' => $seekerEmail,
        'password' => $newPassword,
    ]);
    $assert($newLoginAttempt['status'] === 302, 'New password login should redirect after success.');
    $assert($newLoginAttempt['location'] === '/home', 'New password should authenticate successfully.');

    $homePage = $client->request('GET', '/home');
    $notificationsCsrf = $client->csrfTokenFromBody($homePage['body']);
    $markAllRead = $client->request('POST', '/notifications/read-all', [
        '_token' => $notificationsCsrf,
        'redirect' => '/home',
    ]);
    $assert($markAllRead['status'] === 302, 'Mark all notifications as read should redirect.');
    $assert($markAllRead['location'] === '/home', 'Mark all notifications redirect should return to /home.');

    $seekerUnreadNotifications = (int) (fetchOne($pdo, 'SELECT COUNT(*) AS total FROM notifications WHERE user_id = :user_id AND read_at IS NULL', [
        'user_id' => (int) ($bookingRow['seeker_id'] ?? 0),
    ])['total'] ?? 0);
    $assert($seekerUnreadNotifications === 0, 'Mark all read endpoint should clear all seeker unread notifications.');
} finally {
    if (is_resource($serverProcess)) {
        proc_terminate($serverProcess);
        usleep(200000);
        $status = proc_get_status($serverProcess);
        if (is_array($status) && ($status['running'] ?? false)) {
            proc_terminate($serverProcess, 9);
        }
        proc_close($serverProcess);
    }
}

if ($failures !== []) {
    echo 'FlowRulesTest FAILED:' . PHP_EOL;
    foreach ($failures as $failure) {
        echo '- ' . $failure . PHP_EOL;
    }
    exit(1);
}

echo 'FlowRulesTest PASSED' . PHP_EOL;

function loadEnvironment(): void
{
    $envPath = BASE_PATH . '/.env';
    if (!is_file($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($value !== '' && (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        )) {
            $value = substr($value, 1, -1);
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function createDatabaseConnection(): PDO
{
    $config = require BASE_PATH . '/config/database.php';
    $driver = strtolower((string) ($config['driver'] ?? 'mysql'));

    if ($driver === 'sqlite') {
        $path = (string) ($config['sqlite_path'] ?? BASE_PATH . '/storage/database.sqlite');
        return new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        (string) ($config['host'] ?? '127.0.0.1'),
        (string) ($config['port'] ?? '3306'),
        (string) ($config['database'] ?? 'skillswap'),
        (string) ($config['charset'] ?? 'utf8mb4')
    );

    return new PDO(
        $dsn,
        (string) ($config['username'] ?? 'root'),
        (string) ($config['password'] ?? ''),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
}

function startServer(): array
{
    $phpBinary = PHP_BINARY;
    $devNull = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'NUL' : '/dev/null';
    $descriptorSpec = [
        0 => ['file', $devNull, 'r'],
        1 => ['file', $devNull, 'a'],
        2 => ['file', $devNull, 'a'],
    ];

    for ($attempt = 0; $attempt < 6; $attempt++) {
        $port = random_int(18080, 18990);
        $command = '"' . $phpBinary . '" -S 127.0.0.1:' . $port . ' -t public';

        $process = proc_open($command, $descriptorSpec, $pipes, BASE_PATH);
        if (!is_resource($process)) {
            continue;
        }

        for ($probe = 0; $probe < 60; $probe++) {
            $test = @file_get_contents('http://127.0.0.1:' . $port . '/login');
            if ($test !== false) {
                return [$process, 'http://127.0.0.1:' . $port];
            }

            $status = proc_get_status($process);
            if (!is_array($status) || !($status['running'] ?? false)) {
                break;
            }

            usleep(100000);
        }

        proc_terminate($process);
        proc_close($process);
    }

    throw new RuntimeException('Unable to start PHP built-in server for feature tests.');
}

function fetchOne(PDO $pdo, string $sql, array $params = []): array
{
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    $row = $statement->fetch();

    return is_array($row) ? $row : [];
}
