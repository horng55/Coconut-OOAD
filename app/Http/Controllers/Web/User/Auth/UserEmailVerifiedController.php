<?php

namespace App\Http\Controllers\Web\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class UserEmailVerifiedController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to verify your email.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('verified', true);
        }

        return Inertia::render('Front/VerifiedEmail', [
            'user' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'message' => $request->session()->get('message'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Verify email address from verification link
     * Works for both web and mobile app users
     *
     * @param Request $request
     * @param int $id
     * @param string $hash
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, int $id, string $hash)
    {
        try {
            // Find the user
            $user = User::findOrFail($id);

            // Check if email is already verified
            if ($user->hasVerifiedEmail()) {
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Email already verified.',
                        'data' => [
                            'user' => $user->makeHidden(['password', 'remember_token']),
                            'email_verified' => true,
                        ]
                    ], 200);
                }

                return redirect()->route('dashboard')->with('verified', true);
            }

            // Verify the hash matches the user's email
            $expectedHash = sha1($user->email);
            if (!hash_equals($expectedHash, $hash)) {
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid verification link.',
                    ], 403);
                }

                return redirect()->route('login')->with('error', 'Invalid verification link.');
            }

            // Validate the signed URL (signature and expiration)
            try {
                // Validate signature using the full request URL
                $isValidSignature = URL::hasValidSignature($request->fullUrl());
                
                if (!$isValidSignature) {
                    // Check if it's expired
                    if ($request->has('expires')) {
                        $expires = $request->get('expires');
                        if ($expires && $expires < now()->timestamp) {
                            if ($request->wantsJson() || $request->expectsJson()) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Verification link has expired. Please request a new verification email.',
                                ], 403);
                            }

                            return redirect()->route('login')->with('error', 'Verification link has expired. Please request a new verification email.');
                        }
                    }

                    // Invalid signature
                    if ($request->wantsJson() || $request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid verification link signature. Please request a new verification email.',
                        ], 403);
                    }

                    return redirect()->route('login')->with('error', 'Invalid verification link. Please request a new verification email.');
                }
            } catch (\Exception $sigException) {
                // If signature validation throws an exception, provide helpful error
                if ($request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Verification link is invalid. Please request a new verification email.',
                    ], 403);
                }

                return redirect()->route('login')->with('error', 'Invalid verification link. Please request a new verification email.');
            }

            // Mark email as verified
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            // If user is authenticated, redirect to dashboard
            if (Auth::check() && Auth::id() === $user->id) {
                return redirect()->route('dashboard')->with('verified', true);
            }

            // For API/mobile requests, return JSON
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email verified successfully.',
                    'data' => [
                        'user' => $user->fresh()->makeHidden(['password', 'remember_token']),
                        'email_verified' => true,
                    ]
                ], 200);
            }

            // For web requests, redirect to login with success message
            return redirect()->route('login')->with('verified', true)->with('message', 'Email verified successfully. You can now log in.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            return redirect()->route('login')->with('error', 'User not found.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email verification failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('login')->with('error', 'Email verification failed. Please try again or request a new verification link.');
        }
    }
}
