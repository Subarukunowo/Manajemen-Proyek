<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
        return $next($request);
    }
}
