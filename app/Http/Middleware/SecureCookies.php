<?php

namespace App\Http\Middleware;

use Closure;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class SecureCookies extends Middleware
{
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('Set-Cookie', 'HttpOnly; Secure');

        return $response;
    }
}
