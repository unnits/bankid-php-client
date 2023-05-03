<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum JsonWebKeyType: string
{
    case EC = 'ec';
    case RSA = 'rsa';
}
