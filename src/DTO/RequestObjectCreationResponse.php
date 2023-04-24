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
     * @param string[]|null $uploadUris
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
            new DateTime($data['exp']),
            $data['request_uri'],
            $data['uploadUri'] ?? null,
            $data['uploadUris'] ?? null,
        );
    }
}
