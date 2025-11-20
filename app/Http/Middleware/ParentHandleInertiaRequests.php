<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class ParentHandleInertiaRequests extends Middleware
{
    /**
     * The root Templates that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $sharedData = parent::share($request);

        return array_merge($sharedData, [
            'csrf_token' => csrf_token(),
            'current_parent' => json_decode(session()->get('current_parent')),
            'flash' => [
                'alert' => $request->session()->get('alert'),
            ],
            'ziggy' => fn () => [
                ...(new \Tighten\Ziggy\Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ]);
    }
}

