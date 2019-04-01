<?php

namespace App\Http\Middleware;

use Closure;

class PortalAuth
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
        if ($request->session()->get('authenticated') !== true) {
            return redirect("/")->withError(utrans("errors.notAuthenticated",[],$request));
        }
        return $next($request);
    }
}
