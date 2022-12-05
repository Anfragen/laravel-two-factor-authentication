<?php

namespace Anfragen\TwoFactor\Http\Controllers;

use Illuminate\Http\{JsonResponse, Request, Response};
use Anfragen\TwoFactor\Actions\{ConfirmTwoFactor, DisableTwoFactor, EnableTwoFactor, GenerateRecoveryCodes, LoginTwoFactor, SendLoginCode};
use Illuminate\Support\Facades\{Crypt, Gate};
use Anfragen\TwoFactor\Http\Requests\{ConfirmRequest, EnableRequest, LoginRequest};
use Illuminate\Routing\Controller;

class TwoFactorController extends Controller
{
    /**
     * Login user with two-factor authentication.
     */
    public function login(LoginRequest $request): Response
    {
        app(LoginTwoFactor::class)->handle($request);

        return response()->noContent();
    }

    /**
     * Resend two-factor authentication code to user.
     */
    public function resend(Request $request): JsonResponse
    {
        Gate::authorize('two-factor-sms-or-email');

        app(SendLoginCode::class)->handle($request->user());

        return response()->json(['message' => trans('anfragen::two-factor.resent')]);
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(EnableRequest $request): JsonResponse
    {
        Gate::authorize('two-factor-enable');

        app(EnableTwoFactor::class)->handle($request->user(), $request->type);

        return response()->json(['message' => trans('anfragen::two-factor.enabled')]);
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirm(ConfirmRequest $request): JsonResponse
    {
        Gate::authorize('two-factor-confirm');

        app(ConfirmTwoFactor::class)->handle($request->user(), $request->code);

        return response()->json(['message' => trans('anfragen::two-factor.confirmed')]);
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(Request $request): JsonResponse
    {
        Gate::authorize('two-factor-disable');

        app(DisableTwoFactor::class)->handle($request->user());

        return response()->json(['message' => trans('anfragen::two-factor.disabled')]);
    }

    /**
     * Get the QR Code for the user.
     */
    public function qrCode(Request $request): JsonResponse
    {
        Gate::authorize('two-factor-app');

        $svg = $request->user()->twoFactorQrCodeSvg();
        $url = $request->user()->twoFactorQrCodeUrl();

        return response()->json(['svg' => $svg, 'url' => $url]);
    }

    /**
     * Get the secret key for the user.
     */
    public function secretKey(Request $request): JsonResponse
    {
        Gate::authorize('two-factor-app');

        $secret = Crypt::decrypt($request->user()->two_factor_secret);

        return response()->json(['secretKey' => $secret]);
    }

    /**
     * Get the recovery codes for the user.
     */
    public function recoveryCodes(Request $request): JsonResponse
    {
        Gate::authorize('two-factor-app');

        $codes = $request->user()->recoveryCodes();

        return response()->json(['recoveryCodes' => $codes]);
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function newRecoveryCodes(Request $request): JsonResponse
    {
        Gate::authorize('two-factor-app');

        app(GenerateRecoveryCodes::class)->handle($request->user());

        return response()->json(['message' => trans('anfragen::two-factor.recovery_codes')]);
    }
}
