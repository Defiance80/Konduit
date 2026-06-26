<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->isClientContact()) {
            return redirect()->route('agency.dashboard');
        }

        if (! $user->client_id) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Your account is not linked to a client.']);
        }

        return $next($request);
    }
}
