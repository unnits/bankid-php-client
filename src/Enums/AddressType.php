<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum AddressType: string
{
    case PermanentResidence = 'permanent_residence';
    case SecondaryResidence = 'secondary_residence';
    case Unknown = 'unknown';
}
