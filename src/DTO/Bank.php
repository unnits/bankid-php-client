<?php

declare(strict_types=1);

namespace Unnits\BankId\DTO;

use Unnits\BankId\Enums\BankService;

class Bank
{
    /**
     * @param string $id
     * @param string $title
     * @param string|null $description
     * @param BankLogo[] $bankLogo
     * @param BankService[] $availableServices
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly array $bankLogo,
        public readonly array $availableServices,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function create(array $data): self
    {
        $logos = array_map(
            fn (array $logo) => BankLogo::create($logo),
            $data['available_logo_images']
        );

        $bankServices = array_map(
            fn (string $bankService) => BankService::from(
                // We can receive two values for unique identity. First is UNIQUE_ID, second is UNIQUE_IDENTITY
                // To avoid this duplicity we replace UNIQUE_ID with UNIQUE_IDENTITY to be able to create
                // our own Enum
                // ticket in Bank iD: https://developer.bankid.cz/support/EXT-3386
                preg_replace('/^UNIQUE_ID$/', 'UNIQUE_IDENTITY', strtoupper($bankService))
            ),
            $data['available_services']
        );

        return new self(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'] ?? null,
            bankLogo: $logos,
            availableServices: $bankServices,
        );
    }
}
