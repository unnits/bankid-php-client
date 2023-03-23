<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\AddressType;

class Address
{
    public function __construct(
        public readonly AddressType $type,
        public readonly string $streetName,
        public readonly ?string $streetNumber,
        public readonly string $evidenceNumber,
        public readonly string $buildingApartment,
        public readonly string $city,
        public readonly string $cityArea,
        public readonly string $zipCode,
        public readonly string $country,
        public readonly string $ruianReference,
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
            AddressType::from(strtolower($data['type'])),
            $data['street'],
            $data['streetnumber'] ?? null,
            $data['evidencenumber'],
            $data['buildingapartment'],
            $data['city'],
            $data['cityarea'],
            $data['zipcode'],
            strtolower($data['country']),
            $data['ruian_reference'],
        );
    }
}
