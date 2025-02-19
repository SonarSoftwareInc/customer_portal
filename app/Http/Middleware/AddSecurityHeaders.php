<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next, string $customPolicyClass = null)
    {
        $response = $next($request);
        $response->header('X-Content-Type-Options', 'nosniff', true);
        $response->header('Strict-Transport-Security', 'max-age', true);

        return $response;
    }
}
