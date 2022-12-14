<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class AdminRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $policyName)
    {
        if (auth()->guard("admin-api")->user() && auth()->guard("admin-api")->user()->cannot($policyName, Admin::class)) {
            throw new ForbiddenException();
        }

        return $next($request);
    }
}
