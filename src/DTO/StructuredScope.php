<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use JsonSerializable;

class StructuredScope implements JsonSerializable
{
    public function __construct(
        public readonly ?SignObject $signObject = null,
        public readonly ?DocumentObjects $documentObjects = null,
        public readonly ?DocumentObject $documentObject = null,
    ) {
        //
    }

    /**
     * @return array{'signObject': mixed, 'documentObjects': mixed, 'documentObject': mixed}
     */
    public function jsonSerialize(): array
    {
        return [
            'signObject' => $this->signObject?->jsonSerialize(),
            'documentObjects' => $this->documentObjects?->jsonSerialize(),
            'documentObject' => $this->documentObject?->jsonSerialize(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            array_key_exists('signObject', $data)
                ? SignObject::create($data['signObject'])
                : null,
            array_key_exists('documentObjects', $data)
                ? DocumentObjects::create($data['documentObjects'])
                : null,
            array_key_exists('documentObject', $data)
                ? DocumentObject::create($data['documentObject'])
                : null
        );
    }
}
