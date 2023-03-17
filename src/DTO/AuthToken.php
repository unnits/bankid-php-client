<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\Scope;
use Unnits\BankId\Enums\TokenType;

class AuthToken
{
    /**
     * @param string $value
     * @param TokenType $tokenType
     * @param int $expiresIn
     * @param Scope[] $scopes
     * @param string $tokenId
     */
    public function __construct(
        public readonly string $value,
        public readonly TokenType $tokenType,
        public readonly int $expiresIn,
        public readonly array $scopes,
        public readonly string $tokenId,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        /** @var array<int, Scope|null> $scopes */
        $scopes = array_map(
            fn (string $scope) => Scope::tryFrom($scope),
            explode(' ', $data['scope'] ?? '')
        );

        /** @var Scope[] $scopes */
        $scopes = array_filter($scopes, fn (?Scope $scope) => $scope !== null);

        return new self(
            $data['access_token'],
            TokenType::from(strtolower($data['token_type'])),
            $data['expires_in'],
            $scopes,
            $data['id_token']
        );
    }
}
