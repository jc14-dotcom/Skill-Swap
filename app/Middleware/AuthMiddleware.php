<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Session;

class AuthMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (!is_array(Session::get('auth.user'))) {
            header('Location: /login', true, 302);
            exit;
        }

        return $next();
    }
}