<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum TokenType: string
{
    case Bearer = 'bearer';
    case AccessToken = 'access_token';
    case RefreshToken = 'refresh_token';
}
