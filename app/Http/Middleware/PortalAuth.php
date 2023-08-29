<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('authenticated') !== true) {
            return redirect('/')->withError(utrans('errors.notAuthenticated', [], $request));
        }

        return $next($request);
    }
}
