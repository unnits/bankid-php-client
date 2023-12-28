<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

class TokenInfoException extends BankIdException
{
    public static function message(): string
    {
        return 'Failed getting token info';
    }
}
