<?php

namespace App\Http\Middleware;

use App\Models\AuditLog as AuditLogModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLog
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && $this->shouldLog($request)) {
            AuditLogModel::create([
                'user_id' => auth()->id(),
                'action' => $this->determineAction($request),
                'model_type' => $this->extractModelType($request),
                'model_id' => $this->extractModelId($request),
                'old_values' => null,
                'new_values' => $request->except(['password', 'password_confirmation']),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    /**
     * Determine if the request should be logged.
     */
    protected function shouldLog(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Determine the action type based on HTTP method.
     */
    protected function determineAction(Request $request): string
    {
        return match ($request->method()) {
            'GET' => 'READ',
            'POST' => 'MODIFY',
            'PUT', 'PATCH' => 'MODIFY',
            'DELETE' => 'MODIFY',
            default => 'READ',
        };
    }

    /**
     * Extract the model type from the route.
     */
    protected function extractModelType(Request $request): ?string
    {
        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return null;
        }

        $segments = explode('.', $routeName);
        $base = $segments[0] ?? null;

        return $base ? ucfirst($base) : null;
    }

    /**
     * Extract the model ID from the route parameters.
     */
    protected function extractModelId(Request $request): ?string
    {
        return $request->route('id') ?? $request->route()->parameter('id');
    }
}
