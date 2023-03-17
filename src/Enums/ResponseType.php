<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum ResponseType: string
{
    case Token = 'token';
    case Code = 'code';
}
