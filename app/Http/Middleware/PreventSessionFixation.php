<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class PreventSessionFixation
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            Session::regenerate();
        }

        return $next($request);
    }
}
