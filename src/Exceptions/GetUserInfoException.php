<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

class GetUserInfoException extends BankIdException
{
    public static function message(): string
    {
        return 'Failed getting user info';
    }
}
