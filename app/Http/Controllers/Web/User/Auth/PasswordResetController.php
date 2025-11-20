<?php

namespace App\Http\Controllers\Web\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset form
     */
    public function show(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return Inertia::render('User/PasswordReset', [
                'error' => 'Invalid reset link. Please request a new password reset.',
                'token' => null,
                'email' => null,
            ]);
        }

        return Inertia::render('User/PasswordReset', [
            'token' => $token,
            'email' => $email,
        ]);
    }
}
