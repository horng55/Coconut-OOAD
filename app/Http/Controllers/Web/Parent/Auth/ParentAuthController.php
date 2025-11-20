<?php

namespace App\Http\Controllers\Web\Parent\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ParentUser;
use Illuminate\Auth\Events\Failed;
use Inertia\Inertia;

class ParentAuthController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->parentUser) {
                return redirect()->route('parent.dashboard');
            }
        }

        return Inertia::render('Parent/Auth/Login');
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

        // Check if user is a parent
        $parent = ParentUser::where('user_id', $user->id)->first();
        if (!$parent) {
            event(new Failed('web', $user, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]));
            return back()->withErrors(['email' => 'You do not have parent access. Please use the correct portal.']);
        }

        if ($user->status !== 'active') {
            event(new Failed('web', $user, [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]));
            return back()->withErrors(['email' => 'Your account is not active. Please contact administrator.']);
        }

        Auth::guard('web')->login($user, $request->boolean('remember'));

        session([
            'current_parent' => json_encode($parent->load('user', 'students.user')),
            'last_request' => now(),
        ]);

        return redirect()->route('parent.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

