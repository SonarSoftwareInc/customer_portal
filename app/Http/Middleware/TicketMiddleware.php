<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('customer_portal.ticketing_enabled') !== true) {
            return redirect()->back()->withErrors(utrans('errors.sectionDisabled', [], $request));
        }

        return $next($request);
    }
}
