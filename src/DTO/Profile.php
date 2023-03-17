<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\Gender;

class Profile
{
    /**
     * @param string $givenName
     * @param string $familyName
     * @param Gender $gender
     * @param int $age
     * @param string $birthDate
     * @param string $birthNumber
     * @param string $birthPlace
     * @param string $birthCountry
     * @param string $primaryNationality
     * @param string[] $nationalities
     * @param string $maritalStatus
     * @param string $email
     * @param string $phoneNumber
     * @param bool $limitedLegalCapacity
     * @param Address[] $addresses
     * @param IdCard[] $idCards
     * @param string[] $paymentAccounts
     * @param PaymentAccount[] $paymentAccountsDetails
     * @param int $updatedAt
     */
    public function __construct(
        public readonly string $givenName,
        public readonly string $familyName,
        public readonly Gender $gender,
        public readonly int $age,
        public readonly string $birthDate,
        public readonly string $birthNumber,
        public readonly string $birthPlace,
        public readonly string $birthCountry,
        public readonly string $primaryNationality,
        public readonly array $nationalities,
        public readonly string $maritalStatus,
        public readonly string $email,
        public readonly string $phoneNumber,
        public readonly bool $limitedLegalCapacity,
        public readonly array $addresses,
        public readonly array $idCards,
        public readonly array $paymentAccounts,
        public readonly array $paymentAccountsDetails,
        public readonly int $updatedAt,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        $addresses = array_map(
            fn (array $address) => Address::create($address),
            $data['addresses'] ?? []
        );

        $idCards = array_map(
            fn (array $idCard) => IdCard::create($idCard),
            $data['idcards'] ?? []
        );

        return new self(
            $data['given_name'],
            $data['family_name'],
            Gender::from($data['gender']),
            $data['age'],
            $data['birthdate'],
            $data['birthnumber'],
            $data['birthplace'],
            $data['birthcountry'],
            $data['primary_nationality'],
            $data['nationalities'],
            $data['maritalstatus'],
            $data['email'],
            $data['phone_number'],
            $data['limited_legal_capacity'],
            $addresses,
            $idCards,
            $data['paymentAccounts'],
            $data['paymentAccountsDetails'],
            $data['updated_at'],
        );
    }
}
