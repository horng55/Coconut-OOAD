<?php

namespace App\Http\Controllers\Web\Teacher\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Events\Failed;
use Inertia\Inertia;

class TeacherAuthController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->teacher) {
                return redirect()->route('teacher.dashboard');
            }
        }

        return Inertia::render('Teacher/Auth/Login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            event(new Failed('web', $user, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]));
            return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            event(new Failed('web', $user, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]));
            return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
        }

        // Check if user is a teacher
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) {
            event(new Failed('web', $user, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]));
            return back()->withErrors(['email' => 'You do not have teacher access. Please use the correct portal.']);
        }

        if ($user->status !== 'active' || $teacher->status !== 'active') {
            event(new Failed('web', $user, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]));
            return back()->withErrors(['email' => 'Your account is not active. Please contact administrator.']);
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));

        session([
            'current_teacher' => json_encode($teacher->load('user')),
            'last_request' => now(),
        ]);

        return redirect()->route('teacher.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

