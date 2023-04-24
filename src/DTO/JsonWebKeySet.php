<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\JsonWebKeyUsage;

/**
 * @see https://auth0.com/docs/secure/tokens/json-web-tokens/json-web-key-sets
 */
class JsonWebKeySet
{
    /**
     * @param JsonWebKey[] $keys
     */
    public function __construct(
        public readonly array $keys,
    ) {
        //
    }

    /**
     * @return JsonWebKey[]
     */
    public function getEncryptionKeys(): array
    {
        return array_values(array_filter(
            $this->keys,
            fn (JsonWebKey $key) => $key->usage === JsonWebKeyUsage::Encryption
        ));
    }

    /**
     * @return JsonWebKey[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(array_map(
            fn ($item) => JsonWebKey::create($item),
            $data['keys']
        ));
    }
}
