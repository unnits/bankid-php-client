<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class VerifiedClaims
{
    public function __construct(
        public readonly ?Verification $verification,
        public readonly ?array $claims,
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
            Verification::create($data['verification']) ?? null,
            $data['claims_profile'] ?? null,
        );
    }
}
