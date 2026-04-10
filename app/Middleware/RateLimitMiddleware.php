<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\App;
use Core\Request;
use Core\Session;

class RateLimitMiddleware
{
    public function handle(Request $request, callable $next, string $scope = 'default'): mixed
    {
        $limits = App::getConfig('app.rate_limits.' . $scope, App::getConfig('app.rate_limits.default', [
            'max' => 30,
            'window' => 60,
        ]));

        $maxAttempts = (int) ($limits['max'] ?? 30);
        $windowSeconds = (int) ($limits['window'] ?? 60);

        $key = 'rate_limit.' . $scope . '.' . sha1($request->ip() . '|' . $request->path());
        $bucket = Session::get($key, [
            'count' => 0,
            'expires_at' => time() + $windowSeconds,
        ]);

        if (!is_array($bucket)) {
            $bucket = ['count' => 0, 'expires_at' => time() + $windowSeconds];
        }

        if (time() > (int) ($bucket['expires_at'] ?? 0)) {
            $bucket = ['count' => 0, 'expires_at' => time() + $windowSeconds];
        }

        if ((int) ($bucket['count'] ?? 0) >= $maxAttempts) {
            http_response_code(429);
            echo 'Too many requests. Please try again later.';
            return null;
        }

        $bucket['count'] = (int) ($bucket['count'] ?? 0) + 1;
        Session::put($key, $bucket);

        return $next();
    }
}