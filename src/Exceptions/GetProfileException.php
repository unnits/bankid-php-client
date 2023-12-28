<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

class GetProfileException extends BankIdException
{
    public static function message(): string
    {
        return 'Failed getting profile';
    }
}
