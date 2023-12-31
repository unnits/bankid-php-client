<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Exception;
use JsonSerializable;

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

    /**
     * @return array{'envelope_name': string, 'documents': array<int, array<string, mixed>>}
     */
    public function jsonSerialize(): array
    {
        return [
            'envelope_name' => $this->envelopeName,
            'documents' => array_map(
                fn (DocumentObject $document) => $document->jsonSerialize(),
                $this->documents,
            )
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     * @throws Exception
     */
    public static function create(array $data): self
    {
        assert(is_array($data['documents']));

        return new self(
            envelopeName: strval($data['envelope_name']),
            documents: array_map(
                fn ($item) => DocumentObject::create($item),
                $data['documents'],
            )
        );
    }
}
