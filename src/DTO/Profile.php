<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\Gender;

class Profile
{
    /**
     * @param string|null $customerUuid
     * @param string|null $givenName
     * @param string|null $familyName
     * @param string|null $titlePrefix
     * @param string|null $titleSuffix
     * @param Gender|null $gender
     * @param int|null $age
     * @param string|null $birthDate
     * @param string|null $birthNumber
     * @param string|null $birthPlace
     * @param string|null $birthCountry
     * @param string|null $primaryNationality
     * @param string[]|null $nationalities
     * @param string|null $maritalStatus
     * @param bool|null $majority
     * @param string|null $email
     * @param string|null $phoneNumber
     * @param bool|null $limitedLegalCapacity
     * @param bool|null $pep
     * @param Address[]|null $addresses
     * @param IdCard[]|null $idCards
     * @param string[]|null $paymentAccounts
     * @param string[]|null $paymentAccountsDetails
     * @param int|null $updatedAt
     * @param ?VerifiedClaims $verifiedClaims
     */
    public function __construct(
        public readonly ?string $customerUuid,
        public readonly ?string $givenName,
        public readonly ?string $familyName,
        public readonly ?string $titlePrefix,
        public readonly ?string $titleSuffix,
        public readonly ?Gender $gender,
        public readonly ?int $age,
        public readonly ?string $birthDate,
        public readonly ?string $birthNumber,
        public readonly ?string $birthPlace,
        public readonly ?string $birthCountry,
        public readonly ?string $primaryNationality,
        public readonly ?array $nationalities,
        public readonly ?string $maritalStatus,
        public readonly ?bool $majority,
        public readonly ?string $email,
        public readonly ?string $phoneNumber,
        public readonly ?bool $limitedLegalCapacity,
        public readonly ?bool $pep,
        public readonly ?array $addresses,
        public readonly ?array $idCards,
        public readonly ?array $paymentAccounts,
        public readonly ?array $paymentAccountsDetails,
        public readonly ?int $updatedAt,
        public readonly ?VerifiedClaims $verifiedClaims,
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

        $nationalities = array_map(
            fn($nationality) => strtolower($nationality),
            $data['nationalities'] ?? []
        );

        return new self(
            $data['sub'] ?? null,
            $data['given_name'] ?? null,
            $data['family_name'] ?? null,
            $data['title_prefix'] ?? null,
            $data['title_suffix'] ?? null,
            array_key_exists('gender', $data) ? Gender::from($data['gender']) : null,
            $data['age'] ?? null,
            $data['birthdate'] ?? null,
            $data['birthnumber'] ?? null,
            $data['birthplace'] ?? null,
            array_key_exists('birthcountry', $data) ? strtolower($data['birthcountry']) : null,
            array_key_exists('primary_nationality', $data) ? strtolower($data['primary_nationality']) : null,
            $nationalities,
            $data['maritalstatus'] ?? null,
            $data['majority'] ?? null,
            $data['email'] ?? null,
            $data['phone_number'] ?? null,
            $data['limited_legal_capacity'] ?? null,
            $data['pep'] ?? null,
            $addresses,
            $idCards,
            $data['paymentAccounts'] ?? null,
            $data['paymentAccountsDetails'] ?? null,
            $data['updated_at'] ?? null,
            VerifiedClaims::create($data['verified_claims']) ?? null,
        );
    }
}
