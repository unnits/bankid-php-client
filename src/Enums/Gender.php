<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';
    case Unspecified = 'unspecified';
}
