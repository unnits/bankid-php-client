<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use JsonSerializable;
use Unnits\BankId\Enums\ResponseType;
use Unnits\BankId\Enums\Scope;

class RequestObject implements JsonSerializable
{
    /**
     * @param int $maxAge
     * @param string $bankId
     * @param string $acrValues
     * @param Scope[] $scopes
     * @param ResponseType $responseType
     * @param StructuredScope $structuredScope
     * @param string $txn
     * @param string $state
     * @param string $nonce
     * @param string $clientId
     */
    public function __construct(
        public readonly int $maxAge,
        public readonly string $bankId,
        public readonly string $acrValues,
        public readonly array $scopes,
        public readonly ResponseType $responseType,
        public readonly StructuredScope $structuredScope,
        public readonly string $txn,
        public readonly string $state,
        public readonly string $nonce,
        public readonly string $clientId,
    ) {
        //
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'max_age' => $this->maxAge,
            'bank_id' => $this->bankId,
            'acr_values' => $this->acrValues,
            'scope' => implode(' ', array_map(
                fn (Scope $scope) => $scope->value,
                $this->scopes
            )),
            'response_type' => $this->responseType,
            'structured_scope' => $this->structuredScope->jsonSerialize(),
            'txn' => $this->txn,
            'state' => $this->state,
            'nonce' => $this->nonce,
            'client_id' => $this->clientId,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            $data['max_age'],
            $data['bank_id'],
            $data['acr_values'],
            array_filter(array_map(
                fn (string $item) => Scope::tryFrom($item),
                explode($data['scope'], ' ')
            )),
            $data['response_type'],
            StructuredScope::create($data['structured_scope']),
            $data['txn'],
            $data['state'],
            $data['nonce'],
            $data['client_id'],
        );
    }
}
