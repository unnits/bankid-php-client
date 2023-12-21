<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use DateTime;
use Exception;
use Unnits\BankId\Traceable;

class RequestObjectCreationResponse implements Traceable
{
    /**
     * @param DateTime $expiresAt
     * @param string $requestUri
     * @param string|null $uploadUri
     * @param array<string, string>|null $uploadUris Maps documentId to its upload URI
     * @param string|null $traceId
     */
    public function __construct(
        public readonly DateTime $expiresAt,
        public readonly string $requestUri,
        public readonly ?string $uploadUri = null,
        public readonly ?array $uploadUris = null,
        private readonly ?string $traceId = null,
    ) {
        //
    }

    /**
     * @param array<int|string, mixed> $data
     * @param string|null $traceId
     * @return self
     * @throws Exception
     */
    public static function create(array $data, ?string $traceId = null): self
    {
        return new self(
            new DateTime(sprintf('@%d', $data['exp'])),
            $data['request_uri'],
            $data['upload_uri'] ?? null,
            $data['upload_uris'] ?? null,
            $traceId
        );
    }

    public function getTraceId(): ?string
    {
        return $this->traceId;
    }
}
