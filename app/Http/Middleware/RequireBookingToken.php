<?php

namespace App\Http\Middleware;

use App\Models\BookingToken;
use Closure;

class RequireBookingToken
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user && BookingToken::activeFor($user)) {
            return $next($request);
        }

        $request->session()->put('booking.intended', $request->fullUrl());

        return redirect()->route('token.buy')
            ->with('warning', 'You need an active booking token before you can book a room.');
    }
}
