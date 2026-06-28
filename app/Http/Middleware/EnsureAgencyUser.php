<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgencyUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->isClientContact()) {
            return redirect()->route('client.dashboard');
        }

        if (! $user->tenant_id) {
            return redirect()->route('login')
                ->with('error', 'This account has no agency tenant assigned. Contact your administrator.');
        }

        return $next($request);
    }
}
