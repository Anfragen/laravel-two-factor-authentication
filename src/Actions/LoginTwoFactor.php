<?php

namespace Anfragen\TwoFactor\Actions;

use Anfragen\TwoFactor\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;

class LoginTwoFactor
{
    /**
     * Login two factor authentication for the user.
     */
    public function handle(LoginRequest $request): void
    {
        if ($code = $request->validRecoveryCode()) {
            $request->user()->replaceRecoveryCode($code);
        } elseif (!$request->hasValidCode()) {
            $this->toResponse($request);
        }

        session()->put('two_factor_auth', true);
    }

    /**
     * Create an HTTP response that represents the object.
     */
    protected function toResponse(LoginRequest $request): void
    {
        list($key, $message) = $request->filled('recovery_code')
            ? ['recovery_code', trans('The provided two factor recovery code was invalid.')]
            : ['code', trans('The provided two factor authentication code was invalid.')];

        throw ValidationException::withMessages([
            $key => $message,
        ]);
    }
}
