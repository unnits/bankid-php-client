<?php

declare(strict_types=1);

namespace Unnits\BankId\OAuth2;

/**
 * Response mode values as defined by OAuth 2.0 specification
 * @see https://openid.net/specs/oauth-v2-multiple-response-types-1_0.html#ResponseModes
 */
enum GrantType: string
{
    case AuthorizationCode = 'authorization_code';
    case RefreshToken = 'refresh_token';
    case Implicit = 'implicit';

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
