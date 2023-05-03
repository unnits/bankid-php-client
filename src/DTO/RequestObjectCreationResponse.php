<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use DateTime;
use Exception;

class RequestObjectCreationResponse
{
    /**
     * @param DateTime $expiresAt
     * @param string $requestUri
     * @param string|null $uploadUri
     * @param array<string, string>|null $uploadUris Maps documentId to its upload URI
     */
    public function __construct(
        public readonly DateTime $expiresAt,
        public readonly string $requestUri,
        public readonly ?string $uploadUri = null,
        public readonly ?array $uploadUris = null,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     * @throws Exception
     */
    public static function create(array $data): self
    {
        return new self(
            new DateTime(sprintf('@%d', $data['exp'])),
            $data['request_uri'],
            $data['upload_uri'] ?? null,
            $data['upload_uris'] ?? null,
        );
    }
}
