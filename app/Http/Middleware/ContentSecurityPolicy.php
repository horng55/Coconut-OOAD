<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $csp = [
            'object-src' => "'none'",
            'child-src' => "'none'",
            'form-action' => "'self'",
            'frame-ancestors' => "'none'",
        ];

        $cspHeader = implode('; ', array_map(
            fn($directive, $value) => "$directive $value",
            array_keys($csp),
            $csp
        ));

        if (!$response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', $cspHeader);
        }

        return $response;
    }
}
