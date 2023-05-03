<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use DateTime;
use Exception;
use Unnits\BankId\Enums\AcrValue;

class IdentityToken
{
    public function __construct(
        public readonly string $sub,
        public readonly DateTime $expiresAt,
        public readonly DateTime $issuedAt,
        public readonly DateTime $authenticatedAt,
        public readonly string $iss,
        public readonly string $aud,
        public readonly string $nonce,
        public readonly AcrValue $acr,
        public readonly StructuredScope $structuredScope,
        public readonly string $jti,
        public readonly ?string $sid = null,
        public readonly ?string $name = null,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     * @throws Exception
     */
    public static function create(array $data): self
    {
        return new self(
            sub: $data['sub'],
            expiresAt: new DateTime(sprintf('@%d', $data['exp'])),
            issuedAt: new DateTime(sprintf('@%d', $data['iat'])),
            authenticatedAt: new DateTime(sprintf('@%d', $data['auth_time'])),
            iss: $data['iss'],
            aud: $data['aud'],
            nonce: $data['nonce'],
            acr: AcrValue::from($data['acr']),
            structuredScope: StructuredScope::create($data['structured_scope']),
            jti: $data['jti'],
            sid: $data['sid'] ?? null,
            name: $data['name'] ?? null,
        );
    }
}
