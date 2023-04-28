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
        $signArea = $data['sign_area'];
        assert(is_array($signArea));

        $signField = array_key_exists('sign_field', $data)
            ? strval($data['sign_field'])
            : null;

        $documentPriority = array_key_exists('document_priority', $data)
            ? intval($data['document_priority'])
            : null;

        return new self(
            documentSize: intval($data['document_size']),
            documentLanguage: strval($data['document_language']),
            documentId: strval($data['document_id']),
            documentHash: strval($data['document_hash']),
            documentReadByEndUser: boolval($data['document_read_by_enduser']),
            hashAlgorithm: strval($data['hash_alg']),
            documentCreatedAt: new DateTime(strval($data['document_created'])),
            signArea: SignArea::create($signArea),
            documentTitle: strval($data['document_title']),
            documentSubject: strval($data['document_subject']),
            documentAuthor: strval($data['document_author']),
            documentUri: strval($data['document_uri']),
            documentPriority: $documentPriority,
            signField: $signField,
        );
    }
}
