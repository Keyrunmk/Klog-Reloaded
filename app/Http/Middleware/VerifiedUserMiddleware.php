<?php

namespace App\Http\Middleware;

use App\Exceptions\WebException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $status)
    {
        if (Auth::guard("api")->user()->status !== $status) {
            throw new WebException("Please verify your acoount", 401);
        }
        return $next($request);
    }
}
