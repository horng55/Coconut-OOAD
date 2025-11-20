<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            
            // Redirect to appropriate login based on the route
            if ($request->is('student/*')) {
                return redirect()->route('student.login');
            }
            
            if ($request->is('parent/*')) {
                return redirect()->route('parent.login');
            }
            
            return redirect()->route('admin.index');
        }

        return $next($request);
    }

    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('admin.index');
        }
    }
}

