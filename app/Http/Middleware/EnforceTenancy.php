<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EnforceTenancy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = $request->route()->parameter('tenant');
        $portalModel = Tenant::where('name', $tenant)->firstOrFail();
        $request->route()->forgetParameter('tenant');
        URL::defaults(['tenant' => $tenant]);
        $portalModel->connect();
        return $next($request);
    }
}
