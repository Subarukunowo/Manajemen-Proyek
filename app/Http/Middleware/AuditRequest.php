<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;

class AuditRequest
{
    // Method yang di-log
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    // Route yang dikecualikan (terlalu noisy)
    private const EXCLUDE_ROUTES = ['login', 'logout', 'register'];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log hanya write operations dari user yang sudah login
        if (auth()->check() && in_array($request->method(), self::WRITE_METHODS)) {
            $routeName = $request->route()?->getName() ?? '';

            // Skip login/logout/register
            foreach (self::EXCLUDE_ROUTES as $excluded) {
                if (str_contains($routeName, $excluded)) return $response;
            }

            $action = match($request->method()) {
                'POST'   => 'created',
                'PUT','PATCH' => 'updated',
                'DELETE' => 'deleted',
                default  => 'modified',
            };

            AuditLog::record($action,
                strtoupper($request->method()) . ' ' . $request->path(),
                [
                    'new_values' => $this->sanitize($request->except(['_token','_method','password','password_confirmation'])),
                ]
            );
        }

        return $response;
    }

    private function sanitize(array $data): array
    {
        // Hapus field sensitif
        return array_filter($data, fn($k) => ! in_array($k, ['password','token','secret']), ARRAY_FILTER_USE_KEY);
    }
}
