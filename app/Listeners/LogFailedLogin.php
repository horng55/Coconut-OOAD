<?php

namespace App\Listeners;

use App\Models\UserAction;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;

class LogFailedLogin
{
    public function __construct(private readonly Request $request)
    {
    }

    public function handle(Failed $event): void
    {
        try {
            $username = $event->credentials['email'] ?? $event->credentials['username'] ?? 'unknown';
            
            // Determine portal based on request path
            $portal = 'admin'; // default
            $path = $this->request->path();
            
            if (str_contains($path, 'teacher')) {
                $portal = 'teacher';
            } elseif (str_contains($path, 'student')) {
                $portal = 'student';
            } elseif (str_contains($path, 'parent')) {
                $portal = 'parent';
            }

            $ip = (string) $this->request->ip();

            // Check for duplicate failed login within the last 2 seconds (same username, IP, portal)
            $recentFailedLogin = UserAction::where('action_type', 'login_failed')
                ->where('user_username', $username)
                ->where('portal', $portal)
                ->where('ip', $ip)
                ->where('created_at', '>=', now()->subSeconds(2))
                ->first();

            // Only create log if no recent duplicate exists
            if (!$recentFailedLogin) {
                UserAction::create([
                    'user_username'   => $username,
                    'actionable_id'   => null,
                    'actionable_type' => null,
                    'action_type'     => 'login_failed',
                    'portal'          => $portal,
                    'metadata'        => [
                        'guard' => $event->guard,
                        'user_agent' => (string) $this->request->userAgent(),
                        'path' => $path,
                    ],
                    'ip'              => $ip,
                ]);
            }
        } catch (\Throwable $e) {
            // swallow errors to not break login flow
        }
    }
}

