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
     * @param array<BankLogo> $bankLogo
     * @param BankService[] $availableServices
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly array $bankLogo,
        public readonly array $availableServices,
    ) {
        //
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
            fn (string $bankService) => BankService::from($bankService),
            $data['available_services']
        );

        return new self(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'],
            bankLogo: $logos,
            availableServices: $bankServices,
        );
    }
}
