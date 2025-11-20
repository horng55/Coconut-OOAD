<?php

namespace App\Http\Controllers\Web\Admin\Auth;

use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class RegisteredController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['type'] = 'admin';
        $data['status'] = 'active';

        $user = User::create($data);

        Auth::login($user);

        session([
            'current_admin' => json_encode($user->makeHidden(['password', 'remember_token'])),
            'last_request' => now(),
        ]);

        return redirect()->route('dashboard.index');
    }
}
