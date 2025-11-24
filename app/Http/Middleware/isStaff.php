<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\request;
use symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class isStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role == 'staff') {
            return $next($request);
        } return redirect()->route('home');
    }
};
