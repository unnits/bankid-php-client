<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use DateTime;
use DateTimeInterface;
use Exception;
use JsonSerializable;

class DocumentObject implements JsonSerializable
{
    public function __construct(
        public readonly int $documentSize,
        public readonly string $documentLanguage,
        public readonly string $documentId,
        public readonly string $documentHash,
        public readonly bool $documentReadByEndUser,
        public readonly string $hashAlgorithm,
        public readonly DateTime $documentCreatedAt,
        public readonly SignArea $signArea,
        public readonly string $documentTitle = '',
        public readonly string $documentSubject = '',
        public readonly string $documentAuthor = '',
        public readonly ?string $documentUri = null,
        public readonly ?int $documentPriority = null,
        public readonly ?string $signField = null,
    ) {
        //
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $json = [
            'document_title' => $this->documentTitle,
            'document_size' => $this->documentSize,
            'document_subject' => $this->documentSubject,
            'document_language' => $this->documentLanguage,
            'document_id' => $this->documentId,
            'document_priority' => $this->documentPriority,
            'document_author' => $this->documentAuthor,
            'document_hash' => $this->documentHash,
            'document_read_by_enduser' => $this->documentReadByEndUser,
            'hash_alg' => $this->hashAlgorithm,
            'document_created' => $this->documentCreatedAt->format(DateTimeInterface::ATOM),
            'sign_area' => $this->signArea->jsonSerialize(),
            'sign_field' => $this->signField,
        ];

        if ($this->documentUri !== null) {
            $json['document_uri'] = $this->documentUri;
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
            documentSize: $data['document_size'],
            documentLanguage: $data['document_language'],
            documentId: $data['document_id'],
            documentHash: $data['document_hash'],
            documentReadByEndUser: $data['document_read_by_enduser'],
            hashAlgorithm: $data['hash_alg'],
            documentCreatedAt: new DateTime($data['document_created']),
            signArea: SignArea::create($data['sign_area']),
            documentTitle: $data['document_title'],
            documentSubject: $data['document_subject'],
            documentAuthor: $data['document_author'],
            documentUri: $data['document_uri'],
            documentPriority: $data['document_priority'] ?? null,
            signField: $data['sign_field'] ?? null,
        );
    }
}
