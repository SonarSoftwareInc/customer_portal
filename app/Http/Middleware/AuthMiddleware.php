<?php

namespace App\Http\Middleware;

use Closure;

class AuthMiddleware
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
        if ($request->session()->get('authenticated', false) === true) {
            return $next($request);
        }
        return redirect()->action("AuthenticationController@index")->withErrors(trans("errors.notAuthenticated"));
    }
}
