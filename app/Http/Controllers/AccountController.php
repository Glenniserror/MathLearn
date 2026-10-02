<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Change the authenticated user's password (any role) after verifying
     * their current password. An account still on the random password it
     * got through Google sign-up skips that check, since its owner never
     * knew that password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();
        $hasOwnPassword = $user->hasOwnPassword();

        $validated = $request->validate([
            'current_password' => [Rule::excludeIf(! $hasOwnPassword), 'required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'password_automatically_set' => false,
        ]);

        return response()->json([
            'message' => $hasOwnPassword ? 'Password updated successfully.' : 'Password set. You can now sign in with your email too.',
        ]);
    }
}
