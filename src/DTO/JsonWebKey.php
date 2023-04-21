<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\JsonWebKeyType;
use Unnits\BankId\Enums\JsonWebKeyUsage;

/**
 * @see https://auth0.com/docs/secure/tokens/json-web-tokens/json-web-key-sets
 * @see https://datatracker.ietf.org/doc/html/rfc7517
 * @see https://developer.bankid.cz/docs/api/bankid-for-sep
 */
class JsonWebKey
{
    /**
     * @param JsonWebKeyType $type
     * @param JsonWebKeyUsage $usage
     * @param string[] $chain
     */
    public function __construct(
        public readonly JsonWebKeyType $type,
        public readonly JsonWebKeyUsage $usage,
        public readonly array $chain,
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
            JsonWebKeyType::from(strtolower($data['kty'])),
            JsonWebKeyUsage::from(strtolower($data['use'])),
            $data['x5c']
        );
    }
}
