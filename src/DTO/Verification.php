<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class Verification
{
    public function __construct(
        public readonly ?string $verificationDate,
        public readonly string $verificationBank,
        public readonly string $trustFramework,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return Verification
     */
    public static function create(array $data): self
    {
        return new self(
            $data['time'] ?? null,
            $data['verification_process'],
            $data['trust_framework'],
        );
    }
}
