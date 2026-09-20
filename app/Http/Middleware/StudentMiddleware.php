<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check() || Auth::user()->role !== 'student') {
            return redirect()->route('homepage')
                ->withErrors(['access' => 'Unauthorized access.']);
        }

        if (Auth::user()->approval_status !== 'approved') {
            Auth::logout();

            return redirect()->route('student.login')
                ->withErrors(['email' => 'Your account is awaiting teacher approval. Please check back later.']);
        }

        return $next($request);
    }
}
