<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\Gender;

class UserInfo
{
    public function __construct(
        public readonly string $customerUuid,
        public readonly string $transactionIdentifier,
        public readonly ?VerifiedClaims $verifiedClaims,
        public readonly ?string $name,
        public readonly ?string $givenName,
        public readonly ?string $familyName,
        public readonly ?string $middleName,
        public readonly ?string $nickname,
        public readonly ?string $preferredUsername,
        public readonly ?string $email,
        public readonly ?bool $emailVerified,
        public readonly ?Gender $gender,
        public readonly ?string $birthDate,
        public readonly ?string $timezone,
        public readonly ?string $locale,
        public readonly ?string $phoneNumber,
        public readonly ?bool $phoneNumberVerified,
        public readonly ?string $updatedAt,
    ) {
        //
    }

    /**
     * @param array<int|string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        $verifiedClaims = array_key_exists('verified_claims', $data)
            ? VerifiedClaims::create($data['verified_claims'])
            : null;

        $emailVerified = array_key_exists('email_verified', $data)
            ? (bool)$data['email_verified']
            : null;

        $gender = array_key_exists('gender', $data)
            ? Gender::from($data['gender'])
            : null;

        $phoneVerified = array_key_exists('phone_number_verified', $data)
            ? (bool)$data['phone_number_verified']
            : null;

        $updatedAt = array_key_exists('updated_at', $data)
            ? (string)$data['updated_at']
            : null;

        return new self(
            $data['sub'],
            $data['txn'],
            $verifiedClaims,
            $data['name'] ?? null,
            $data['givenName'] ?? null,
            $data['familyName'] ?? null,
            $data['middleName'] ?? null,
            $data['nickname'] ?? null,
            $data['preferred_username'] ?? null,
            $data['email'] ?? null,
            $emailVerified,
            $gender,
            $data['birthdate'] ?? null,
            $data['zoneinfo'] ?? null,
            $data['locale'] ?? null,
            $data['phone_number'] ?? null,
            $phoneVerified,
            $updatedAt,
        );
    }
}
