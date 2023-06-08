<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class BankMetadata
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public ?string $companyIdentificationNumber = null,
        public ?string $verificationDate = null,
    ) {
        //
    }

    /**
     * @return array{
     *     'id': string,
     *     'name': string,
     *     'companyIdentificationNumber': string|null,
     *     'verificationDate': string|null
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'companyIdentificationNumber' => $this->companyIdentificationNumber,
            'verificationDate' => $this->verificationDate,
        ];
    }

    /**
     * @param array{
     *     'id': string,
     *     'name': string,
     *     'verificationClaim': string|null,
     *     'verificationDate': string|null
     * } $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            companyIdentificationNumber: $data['verificationClaim'] ?? null,
            verificationDate: $data['verificationDate'] ?? null,
        );
    }
}
