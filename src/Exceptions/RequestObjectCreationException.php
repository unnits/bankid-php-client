<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

class RequestObjectCreationException extends BankIdException
{
    public static function message(): string
    {
        return 'Failed creating request object';
    }
}
