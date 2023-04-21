<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class DocumentObjects implements JsonSerializable
{
    /**
     * @param string $envelopeName
     * @param DocumentObject[] $documents
     */
    public function __construct(
        public readonly string $envelopeName,
        public readonly array $documents,
    ) {
        //
    }

    public function jsonSerialize(): array
    {
        return [
            'envelope_name' => $this->envelopeName,
            'documents', array_map(
                fn (DocumentObject $document) => $document->jsonSerialize(),
                $this->documents,
            )
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            $data['envelope_name'],
            array_map(
                fn ($item) => DocumentObject::create($item),
                $data['documents'],
            )
        );
    }
}