<?php

namespace Anfragen\TwoFactor\Http\Middleware;

use Closure;
use Illuminate\Http\{Request, Response};

class CheckTwoFactorRequired
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()->hasEnabledTwoFactorAuthentication() && session()->has('two_factor_auth') === false) {
            abort(Response::HTTP_FOUND, trans('anfragen::two-factor.redirect'));
        }

        return $next($request);
    }
}
