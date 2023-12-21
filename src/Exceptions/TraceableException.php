<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

use Exception;
use Throwable;

class TraceableException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public readonly ?string $traceId = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
