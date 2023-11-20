<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum ClientAssertionType: string
{
    case JwtBearer = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';
}
