<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class SignObject implements JsonSerializable
{
    /**
     * @param SignObjectField[] $fields
     */
    public function __construct(
        public readonly array $fields,
    ) {
        //
    }

    public function jsonSerialize(): array
    {
        return [
            'fields' => array_map(
                fn (SignObjectField $field) => $field->jsonSerialize(),
                $this->fields,
            )
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(array_map(
            fn ($item) => SignObjectField::create($item),
            $data['fields'],
        ));
    }
}