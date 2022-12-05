<?php

namespace Anfragen\TwoFactor\Tests\Models;

use Anfragen\TwoFactor\Tests\Factories\UserFactory;
use Anfragen\TwoFactor\Traits\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\{Factory, HasFactory};
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use TwoFactorAuthenticatable;

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }
}
