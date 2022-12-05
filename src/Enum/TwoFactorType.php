<?php

namespace Anfragen\TwoFactor\Enum;

enum TwoFactorType: string
{
    case APP   = 'app';
    case SMS   = 'sms';
    case EMAIL = 'email';
}
