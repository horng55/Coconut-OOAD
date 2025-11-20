<?php

namespace App\Listeners;

use App\Models\UserAction;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public function __construct(private readonly Request $request)
    {
    }

    public function handle(Login $event): void
    {
        try {
            $user = $event->user;
            $username = $user->username ?? $user->email ?? (string) $user->id;

            // Determine portal based on user type and request path
            $portal = 'admin'; // default
            $path = $this->request->path();
            
            if (str_contains($path, 'teacher')) {
                $portal = 'teacher';
            } elseif (str_contains($path, 'student')) {
                $portal = 'student';
            } elseif (str_contains($path, 'parent')) {
                $portal = 'parent';
            } elseif ($user->type === 'admin') {
                $portal = 'admin';
            } elseif ($user->type === 'teacher') {
                $portal = 'teacher';
            } elseif ($user->type === 'student') {
                $portal = 'student';
            } elseif ($user->type === 'parent') {
                $portal = 'parent';
            }

            $ip = (string) $this->request->ip();

            // Check for duplicate login within the last 2 seconds (same user, IP, portal)
            $recentLogin = UserAction::where('action_type', 'login')
                ->where('user_username', $username)
                ->where('actionable_id', $user->id)
                ->where('portal', $portal)
                ->where('ip', $ip)
                ->where('created_at', '>=', now()->subSeconds(2))
                ->first();

            // Only create log if no recent duplicate exists
            if (!$recentLogin) {
                UserAction::create([
                    'user_username'   => $username,
                    'actionable_id'   => $user->id,
                    'actionable_type' => get_class($user),
                    'action_type'     => 'login',
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
            // swallow errors to not break login
        }
    }
}


