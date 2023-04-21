<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use DateTime;
use JsonSerializable;

class DocumentObject implements JsonSerializable
{
    public function __construct(
        public readonly string $documentTitle,
        public readonly int $documentSize,
        public readonly string $documentSubject,
        public readonly string $documentLanguage,
        public readonly string $documentId,
        public readonly string $documentAuthor,
        public readonly string $documentHash,
        public readonly bool $documentReadByEndUser,
        public readonly string $hashAlgorithm,
        public readonly DateTime $documentCreatedAt,
        public readonly SignArea $signArea,
        public readonly string $documentUri,
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
        return [
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
            'document_created' => $this->documentCreatedAt,
            'sign_area' => $this->signArea->jsonSerialize(),
            'sign_field' => $this->signField,
            'document_uri' => $this->documentUri,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            $data['document_title'],
            $data['document_size'],
            $data['document_subject'],
            $data['document_language'],
            $data['document_id'],
            $data['document_author'],
            $data['document_hash'],
            $data['document_read_by_enduser'],
            $data['hash_alg'],
            new DateTime($data['document_created']),
            SignArea::create($data['sign_area']),
            $data['document_uri'],
            $data['document_priority'] ?? null,
            $data['sign_field'] ?? null,
        );
    }
}
