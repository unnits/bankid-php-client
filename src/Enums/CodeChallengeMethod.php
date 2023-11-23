<?php

declare(strict_types=1);

namespace Unnits\BankId\Enums;

enum CodeChallengeMethod: string
{
    case Plain = 'plain';
    case S256 = 'S256';

    /**
     * @param string[] $items
     * @return self[]
     */
    public static function collectionFromArray(array $items): array
    {
        /** @var array<int, self|null> $items */
        $items = array_map(
            fn (string $item) => self::tryFrom($item),
            $items
        );

        /** @var self[] $items */
        $items = array_filter($items, fn (?self $item) => $item !== null);

        return $items;
    }
}
