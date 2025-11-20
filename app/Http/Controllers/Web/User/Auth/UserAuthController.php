<?php

namespace App\Http\Controllers\Web\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserAuthController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return inertia('User/Login');
    }

    public function showRegistrationForm()
    {
        return inertia('User/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->whereNull('deleted_at'),
            ],
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::withTrashed()->where('email', $request->email)->first();

        if ($user) {
            if ($user->trashed()) {
                $user->restore();

                $user->update([
                    'name' => $request->name,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'status' => 'active',
                    'type' => 'user',
                ]);
            } else {
                return back()->withErrors(['email' => 'This email is already registered.']);
            }
        } else {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
                'type' => 'user',
            ]);
            $user->sendEmailVerificationNotification();
        }

        Auth::login($user);

        session([
            'current_user' => json_encode($user->makeHidden(['password', 'remember_token'])),
            'last_request' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || $user->status !== 'active') {
            return back()->withErrors(['email' => 'The provided credentials are incorrect or your account is inactive.']);
        }

        Auth::login($user, $request->remember);

        session([
            'current_user' => json_encode($user->makeHidden(['password', 'remember_token'])),
            'last_request' => now(),
        ]);

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.index');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
