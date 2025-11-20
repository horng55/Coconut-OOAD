<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

class PreventClickjacking
{
    public function handle($request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'DENY');
        return $response;
    }
}
