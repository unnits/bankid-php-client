<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum AuthStrategy
{
    case PlainSecret;
    case SignedWithBankIdSecret;
    case SignedWithOwnJWK;
}
