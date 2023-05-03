<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum JsonWebKeyUsage: string
{
    case Encryption = 'enc';
    case Signature = 'sig';
    case Unknown = 'unknown';
}
