<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Manager
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isStaff()) {
            abort(403, 'You do not have staff access.');
        }

        return $next($request);
    }
}