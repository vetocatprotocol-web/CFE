<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCustomer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = auth()->user();

        if ($user->role?->name === 'admin' || $user->role?->name === 'owner') {
            return $next($request);
        }

        if ($user->role?->name === 'customer') {
            return $this->ensureOwnDataAccess($request, $next, $user);
        }

        if (in_array($user->role?->name, ['dokter', 'kasir'])) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Unauthorized. Customer access required.');
        }

        return redirect()->back()->with('error', 'You do not have access to the customer portal.');
    }

    /**
     * Ensure customer users can only access their own data.
     */
    protected function ensureOwnDataAccess(Request $request, Closure $next, $user): Response
    {
        $customer = Customer::where('user_id', $user->id)->first();

        if (! $customer) {
            if ($request->expectsJson()) {
                abort(404, 'Customer profile not found.');
            }

            return redirect()->route('home')->with('error', 'Customer profile not found.');
        }

        $customerId = $request->route('customer_id') ?? $request->route('customer')?->id;

        if ($customerId && (int) $customerId !== $customer->id) {
            if ($request->expectsJson()) {
                abort(403, 'You can only access your own data.');
            }

            return redirect()->back()->with('error', 'You can only access your own data.');
        }

        $request->merge(['auth_customer_id' => $customer->id]);

        return $next($request);
    }
}
