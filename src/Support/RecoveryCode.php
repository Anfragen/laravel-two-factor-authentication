<?php

namespace Anfragen\TwoFactor\Support;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\{Collection, Str};

class RecoveryCode
{
    /**
     * Generate a new recovery code.
     */
    public static function generate(): string
    {
        return Str::random(10) . '-' . Str::random(10);
    }

    /**
     * Generate multiple recovery codes.
     */
    public static function generateMany(): string
    {
        $codes = Collection::times(10, fn () => self::generate())->all();

        return Crypt::encrypt(json_encode($codes));
    }
}
