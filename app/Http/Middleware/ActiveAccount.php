<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()->active) {
            if($request->route()->getName() === 'inactive') {
                return redirect()->route('home');
            }
            return $next($request);
        } else {
            if ($request->route()->getName() === 'inactive') {
                return $next($request);
            }
            return redirect()->route('inactive');
        }
    }
}
