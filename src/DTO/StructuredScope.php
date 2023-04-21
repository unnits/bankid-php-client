<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class StructuredScope implements JsonSerializable
{
    public function __construct(
        public readonly SignObject $signObject,
        public readonly ?DocumentObjects $documentObjects = null,
        public readonly ?DocumentObject $documentObject = null,
    ) {
        //
    }

    public function jsonSerialize(): array
    {
        return [
            'signObject' => $this->signObject->jsonSerialize(),
            'documentObjects' => $this->documentObjects->jsonSerialize(),
            'documentObject' => $this->documentObject->jsonSerialize(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            SignObject::create($data['signObject']),
            array_key_exists('documentObjects', $data)
                ? DocumentObjects::create($data['documentObjects'])
                : null,
            array_key_exists('documentObject', $data)
                ? DocumentObject::create($data['documentObject'])
                : null
        );
    }
}