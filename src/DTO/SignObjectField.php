<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use \JsonSerializable;

class SignObjectField implements JsonSerializable
{
    public function __construct(
        public readonly int $priority,
        public readonly string $value,
        public readonly string $key,
    ) {
        //
    }

    /**
     * @return array{'priority': int, 'value': string, 'key': string}
     */
    public function jsonSerialize(): array
    {
        return [
            'priority' => $this->priority,
            'value' => $this->value,
            'key' => $this->key,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            $data['priority'],
            $data['value'],
            $data['key'],
        );
    }
}