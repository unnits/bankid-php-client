<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum IdCardType: string
{
    case ID = 'id';
    case Passport = 'p';
}
