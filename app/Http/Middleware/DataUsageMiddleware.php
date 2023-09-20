<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataUsageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('customer_portal.data_usage_enabled') !== true) {
            return redirect()->back()->withErrors(trans('errors.sectionDisabled'));
        }

        return $next($request);
    }
}
