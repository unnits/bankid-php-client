<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

class BankLogo
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly int $width,
        public readonly int $height,
    ) {
        //
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        return new self(
            id: $data['id'],
            url: $data['url'],
            width: $data['width'],
            height: $data['height'],
        );
    }
}
