<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Session;

class CsrfMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next();
        }

        $token = (string) $request->input('_token', (string) $request->header('X-CSRF-TOKEN', ''));
        $sessionToken = Session::token();

        if ($sessionToken === '' || !hash_equals($sessionToken, $token)) {
            http_response_code(419);
            echo 'CSRF token mismatch.';
            return null;
        }

        return $next();
    }
}