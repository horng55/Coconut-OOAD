<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\IdCard;
use Inertia\Middleware;

class TeacherHandleInertiaRequests extends Middleware
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

        // Get current teacher and ID card if authenticated
        $currentTeacher = null;
        $idCard = null;
        
        if (Auth::check()) {
            $user = Auth::user();
            $teacher = Teacher::where('user_id', $user->id)->with('user')->first();
            
            if ($teacher) {
                $currentTeacher = $teacher;
                
                $idCard = IdCard::where('teacher_id', $teacher->id)
                    ->where('status', 'active')
                    ->with('teacher.user')
                    ->latest('issue_date')
                    ->first();
            }
        }

        return array_merge($sharedData, [
            'csrf_token' => csrf_token(),
            'current_teacher' => $currentTeacher,
            'teacher_id_card' => $idCard,
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

