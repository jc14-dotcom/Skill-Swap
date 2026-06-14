<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\Session;

class GuestMiddleware
{
    public function handle(Request $request, callable $next): mixed
    {
        if (is_array(Session::get('auth.user'))) {
            $response = new Response(BASE_PATH);
            $response->redirect('/home', 302);
            return null;
        }

        return $next();
    }
}
