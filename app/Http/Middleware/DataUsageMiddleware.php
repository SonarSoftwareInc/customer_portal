<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class DataUsageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config("customer_portal.data_usage_enabled") !== true) {
            return redirect()->back()->withErrors(trans("errors.sectionDisabled"));
        }
        return $next($request);
    }
}
