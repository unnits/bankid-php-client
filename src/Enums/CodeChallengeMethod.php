<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum CodeChallengeMethod: string
{
    case Plain = 'plain';
    case S256 = 'S256';
}
