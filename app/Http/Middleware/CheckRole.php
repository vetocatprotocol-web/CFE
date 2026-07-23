<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = auth()->user();

        if (! in_array($user->role?->name, $roles)) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized. You do not have the required role.');
            }

            return redirect()->back()->with('error', 'You do not have the required role to access this page.');
        }

        return $next($request);
    }
}
