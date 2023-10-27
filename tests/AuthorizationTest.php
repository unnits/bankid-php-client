<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Tests\Factories\AuthorizationUriFactory;
use Unnits\BankId\Enums\Scope;

class AuthorizationTest extends TestCase
{
    public function test_authorization_uri_is_prefixed_with_base_uri(): void
    {
        $baseUri = 'https://oidc.sandbox.bankid.cz';
        $uri = AuthorizationUriFactory::create(baseUri: $baseUri)->__toString();

        $this->assertStringStartsWith(
            prefix: $baseUri,
            string: $uri,
            message: 'Resulting authentication URI should start with the provided base uri'
        );
    }

    public function test_invalid_scopes_get_thrown_away(): void
    {
        $validScopes = [Scope::OpenId, Scope::Name];
        $scopes = array_merge($validScopes, ['these', 'scopes', 'are', 'invalid']);

        // @phpstan-ignore-next-line
        $uri = AuthorizationUriFactory::create(scopes: $scopes)->__toString();

        $queryString = parse_url($uri, PHP_URL_QUERY);
        assert(is_string($queryString));

        parse_str($queryString, $query);

        assert(is_string($query['scope']));

        $scopes = explode(' ', $query['scope']);

        $this->assertEqualsCanonicalizing(
            array_map(fn (Scope $scope) => $scope->value, $validScopes),
            $scopes,
        );
    }
}
