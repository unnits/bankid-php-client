<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\IdCardType;

class IdCard
{
    public function __construct(
        public readonly IdCardType $type,
        public readonly string $description,
        public readonly string $country,
        public readonly string $number,
        public readonly string $validTo,
        public readonly string $issuedBy,
        public readonly string $issuedAt,
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
            IdCardType::from(strtolower($data['type'])),
            $data['description'],
            $data['country'],
            $data['number'],
            $data['valid_to'],
            $data['issuer'],
            $data['issue_date'],
        );
    }
}
