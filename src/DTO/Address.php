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
     * @param array<string, string> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            type: array_key_exists('type', $data) ? AddressType::from(strtolower($data['type'])) : null,
            streetName: $data['street'] ?? null,
            streetNumber: $data['streetnumber'] ?? null,
            evidenceNumber: $data['evidencenumber'] ?? null,
            buildingApartment: $data['buildingapartment'] ?? null,
            city: $data['city'] ?? null,
            cityArea: $data['cityarea'] ?? null,
            zipCode: $data['zipcode'] ?? null,
            country: array_key_exists('country', $data) ? strtolower($data['country']) : null,
            ruianReference: $data['ruian_reference'] ?? null,
        );
    }
}
