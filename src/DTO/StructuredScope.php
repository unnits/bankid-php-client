<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Exception;
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
     * @return array{'signObject'?: mixed, 'documentObjects'?: mixed, 'documentObject'?: mixed}
     */
    public function jsonSerialize(): array
    {
        $json = [];

        $signObject = $this->signObject?->jsonSerialize();
        $documentObject = $this->documentObject?->jsonSerialize();
        $documentObjects = $this->documentObjects?->jsonSerialize();

        if ($signObject !== null) {
            $json['signObject'] = $signObject;
        }

        if ($documentObjects !== null) {
            $json['documentObjects'] = $documentObjects;
        } elseif ($documentObject !== null) {
            $json['documentObject'] = $documentObject;
        }

        return $json;
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     * @throws Exception
     */
    public static function create(array $data): self
    {
        return new self(
            array_key_exists('signObject', $data) && is_array($data['signObject'])
                ? SignObject::create($data['signObject'])
                : null,
            array_key_exists('documentObjects', $data) && is_array($data['documentObjects'])
                ? DocumentObjects::create($data['documentObjects'])
                : null,
            array_key_exists('documentObject', $data) && is_array($data['documentObject'])
                ? DocumentObject::create($data['documentObject'])
                : null
        );
    }
}
