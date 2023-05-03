<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\IdCardType;

class IdCard
{
    public function __construct(
        public readonly ?IdCardType $type,
        public readonly ?string $description,
        public readonly ?string $country,
        public readonly ?string $number,
        public readonly ?string $validTo,
        public readonly ?string $issuedBy,
        public readonly ?string $issuedAt,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        assert(is_string($data['description']) || is_null($data['description']));

        return new self(
            array_key_exists('type', $data) ? IdCardType::from(strtolower($data['type'])) : null,
            $data['description'] ?? null,
            array_key_exists('country', $data) ? strtolower($data['country']) : null,
            $data['number'] ?? null,
            $data['valid_to'] ?? null,
            $data['issuer'] ?? null,
            $data['issue_date'] ?? null,
        );
    }
}
