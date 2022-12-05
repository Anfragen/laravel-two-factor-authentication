<?php

namespace Anfragen\TwoFactor\Http\Middleware;

use Closure;
use Illuminate\Http\{Request, Response};

class CheckTwoFactorConfirmed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$request->user()->hasEnabledTwoFactorAuthentication()) {
            abort(Response::HTTP_FORBIDDEN, trans('anfragen::two-factor.required'));
        }

        if (session()->has('two_factor_auth') === true) {
            abort(Response::HTTP_FOUND, trans('anfragen::two-factor.already'));
        }

        return $next($request);
    }
}
