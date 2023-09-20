<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('authenticated', false) === true) {
            return $next($request);
        }

        return redirect()->action([\App\Http\Controllers\AuthenticationController::class, 'index'])->withErrors(trans('errors.notAuthenticated'));
    }
}
