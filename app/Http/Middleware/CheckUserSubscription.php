<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckUserSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($request->routeIs('subscription.*') || $request->routeIs('logout') || $request->routeIs('setting')) {
            return $next($request);
        }

        $subscription = DB::table('user_subscriptions')
            ->where('user_username', $user->username)
            ->where('approval_status', 'approved')
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return redirect()->route('subscription.index')->with('error', 'Subscription approval required.');
        }

        if ($subscription->end_at && Carbon::parse($subscription->end_at)->isPast()) {
            return redirect()->route('subscription.index')->with('error', 'Your subscription has expired.');
        }

        return $next($request);
    }
}
