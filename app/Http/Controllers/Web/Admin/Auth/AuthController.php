<?php

namespace App\Http\Controllers\Web\Admin\Auth;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Support\Service\FlashMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Exception;

class AuthController extends Controller
{
    public function create()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->type === 'admin') {
            return redirect()->route('dashboard.index');
        }

        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        try {
            \Log::info('Login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            // Validate request
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            \Log::info('Validation passed', ['email' => $validated['email']]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                \Log::warning('User not found', ['email' => $validated['email']]);
                // Trigger failed login event
                event(new Failed('web', $user, [
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]));
                return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
            }

            \Log::info('User found', [
                'id' => $user->id,
                'email' => $user->email,
                'type' => $user->type,
                'status' => $user->status,
            ]);

            if (!Hash::check($validated['password'], $user->password)) {
                \Log::warning('Password mismatch', ['email' => $validated['email']]);
                // Trigger failed login event
                event(new Failed('web', $user, [
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                ]));
                return back()->withErrors(['email' => 'The provided credentials are incorrect.']);
            }

            if ($user->type !== 'admin') {
                \Log::warning('User is not admin', [
                    'email' => $validated['email'],
                    'type' => $user->type,
                ]);
                return back()->withErrors(['email' => 'You do not have the required permissions to access this area.']);
            }

            if ($user->status !== 'active') {
                \Log::warning('User account not active', [
                    'email' => $validated['email'],
                    'status' => $user->status,
                ]);
                return back()->withErrors(['email' => 'Your account is not active. Please contact administrator.']);
            }

            Auth::guard('web')->login($user, $request->boolean('remember'));

            \Log::info('User authenticated successfully', ['user_id' => $user->id]);

            session([
                'current_admin' => json_encode($user->makeHidden(['password', 'remember_token'])),
                'last_request' => now(),
            ]);

            FlashMessage::success("Login successful");
            return Redirect::route('dashboard.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors());
        } catch (Exception $exception) {
            \Log::error('Login exception', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            FlashMessage::error($exception->getMessage());
            return Redirect::back();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.index');
    }
}
