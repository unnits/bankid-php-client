<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use DateTime;
use Exception;
use Unnits\BankId\Enums\Scope;
use Unnits\BankId\Enums\TokenType;

class TokenInfo
{
    /**
     * @param bool $isActive
     * @param Scope[] $scopes
     * @param string $clientId
     * @param TokenType $tokenType
     * @param DateTime $expiresAt
     * @param DateTime $issuedAt
     * @param string $sub
     * @param string $iss
     */
    public function __construct(
        public readonly bool $isActive,
        public readonly array $scopes,
        public readonly string $clientId,
        public readonly TokenType $tokenType,
        public readonly DateTime $expiresAt,
        public readonly DateTime $issuedAt,
        public readonly string $sub,
        public readonly string $iss,
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
        $scopes = Scope::collectionFromString($data['scope'] ?? '');

        return new self(
            isActive: boolval($data['active']),
            scopes: $scopes,
            clientId: strval($data['client_id']),
            tokenType: TokenType::from(strtolower(strval($data['token_type']))),
            expiresAt: new DateTime(sprintf('@%d', $data['exp'])),
            issuedAt: new DateTime(sprintf('@%d', $data['iat'])),
            sub: strval($data['sub']),
            iss: strval($data['iss']),
        );
    }
}
