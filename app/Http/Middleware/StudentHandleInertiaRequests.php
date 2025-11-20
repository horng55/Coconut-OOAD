<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class StudentHandleInertiaRequests extends Middleware
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

        // Get current student if authenticated
        $currentStudent = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $student = \App\Models\Student::where('user_id', $user->id)->with('user')->first();
            
            if ($student) {
                $currentStudent = $student;
            }
        }

        return array_merge($sharedData, [
            'csrf_token' => csrf_token(),
            'current_student' => $currentStudent,
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

