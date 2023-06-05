<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\VerificationTrustFramework;

class Verification
{
    public function __construct(
        public readonly ?string $verificationDate,
        public readonly ?string $verificationBank,
        public readonly ?VerificationTrustFramework $trustFramework,
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
            $data['time'] ?? null,
            $data['verification_process'] ?? null,
            $data['trust_framework'] ?? null,
        );
    }
}
