<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

class LogoutException extends BankIdException
{
    public static function message(): string
    {
        return 'Failed logging user out';
    }
}
