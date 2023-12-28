<?php

declare(strict_types=1);

namespace Unnits\BankId\Exceptions;

use Throwable;

class BankIdException extends TraceableException
{
    public const MESSAGE_MASK = "%s.\n\tStatus: %d;\n\tError: %s;\n\t Description: %s;\n\t Trace ID: %s";

    /**
     * @see https://phpstan.org/blog/solving-phpstan-error-unsafe-usage-of-new-static#make-the-constructor-final
     */
    final public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?string $traceId = null,
        public readonly ?int $statusCode = null,
        public readonly ?string $error = null,
        public readonly ?string $description = null,
    ) {
        parent::__construct($message, $code, $previous, $traceId);
    }

    public static function create(BankIdException $e, ?string $message = null): static
    {
        return new static(
            message: sprintf(
                self::MESSAGE_MASK,
                $message ?? static::message(),
                $e->statusCode,
                $e->error ?? 'null',
                $e->description ?? 'null',
                $e->traceId ?? 'null'
            ),
            traceId: $e->traceId,
            statusCode: $e->statusCode,
            error: $e->error,
            description: $e->description,
        );
    }

    public static function message(): string
    {
        return static::class;
    }
}
