<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class PaymentAccount
{
    public function __construct(
        public readonly string $iban,
        public readonly string $currency,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            $data['iban'],
            $data['currency']
        );
    }
}
