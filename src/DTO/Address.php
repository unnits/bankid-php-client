<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\AddressType;

class Address
{
    public function __construct(
        public readonly ?AddressType $type,
        public readonly ?string $streetName,
        public readonly ?string $streetNumber,
        public readonly ?string $evidenceNumber,
        public readonly ?string $buildingApartment,
        public readonly ?string $city,
        public readonly ?string $cityArea,
        public readonly ?string $zipCode,
        public readonly ?string $country,
        public readonly ?string $ruianReference,
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
            array_key_exists('type', $data) ? AddressType::from(strtolower($data['type'])) : null,
            $data['street'] ?? null,
            $data['streetnumber'] ?? null,
            $data['evidencenumber'] ?? null,
            $data['buildingapartment'] ?? null,
            $data['city'] ?? null,
            $data['cityarea'] ?? null,
            $data['zipcode'] ?? null,
            array_key_exists('country', $data) ? strtolower($data['country']) : null,
            $data['ruian_reference'] ?? null,
        );
    }
}
