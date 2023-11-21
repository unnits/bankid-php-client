<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Exception;
use GuzzleHttp\Utils;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Unnits\BankId\Enums\Scope;
use Unnits\BankId\Enums\TokenType;

class AuthToken
{
    /**
     * @param string $value
     * @param TokenType $tokenType
     * @param int $expiresIn
     * @param Scope[] $scopes
     * @param IdentityToken $identityToken
     */
    public function __construct(
        public readonly string $value,
        public readonly TokenType $tokenType,
        public readonly int $expiresIn,
        public readonly array $scopes,
        public readonly IdentityToken $identityToken,
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
        /** @var array<int, Scope|null> $scopes */
        $scopes = array_map(
            fn (string $scope) => Scope::tryFrom($scope),
            explode(' ', strval($data['scope'] ?? ''))
        );

        /** @var Scope[] $scopes */
        $scopes = array_filter($scopes, fn (?Scope $scope) => $scope !== null);

        $serializerManager = new JWSSerializerManager([
            new CompactSerializer()
        ]);

        $rawIdToken = strval($data['id_token']);
        $jwt = $serializerManager->unserialize($rawIdToken);
        $payload = Utils::jsonDecode($jwt->getPayload() ?? '', assoc: true);

        assert(is_array($payload));

        return new self(
            value: strval($data['access_token']),
            tokenType: TokenType::from(strtolower(strval($data['token_type']))),
            expiresIn: intval($data['expires_in']),
            scopes: $scopes,
            identityToken: IdentityToken::create($payload, $rawIdToken),
        );
    }
}
