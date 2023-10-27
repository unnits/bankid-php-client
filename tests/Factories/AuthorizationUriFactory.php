<?php

declare(strict_types=1);

namespace Tests\Factories;

use Unnits\BankId\AuthorizationUri;
use Unnits\BankId\Enums\AcrValue;
use Unnits\BankId\Enums\CodeChallengeMethod;
use Unnits\BankId\Enums\ResponseType;
use Unnits\BankId\Enums\Scope;

class AuthorizationUriFactory
{
    /**
     * @param string $baseUri
     * @param string $clientId
     * @param string $redirectUri
     * @param string $state
     * @param string|null $bankId
     * @param ResponseType $responseType
     * @param CodeChallengeMethod $codeChallengeMethod
     * @param AcrValue $acrValue
     * @param Scope[] $scopes
     * @param string|null $requestUri
     * @return AuthorizationUri
     */
    public static function create(
        string $baseUri = 'https://oidc.sandbox.bankid.cz',
        string $clientId = 'd1f83803-b7d4-413e-a469-11b09e966a31',
        string $redirectUri = 'https://example.com/api/bankid/callback',
        string $state = '1234',
        ?string $bankId = null,
        ResponseType $responseType = ResponseType::Code,
        CodeChallengeMethod $codeChallengeMethod = CodeChallengeMethod::Plain,
        AcrValue $acrValue = AcrValue::LOA2,
        array $scopes = [Scope::OpenId],
        ?string $requestUri = null,
    ): AuthorizationUri
    {
        return new AuthorizationUri(
            $baseUri,
            $clientId,
            $redirectUri,
            $state,
            $bankId,
            $responseType,
            $codeChallengeMethod,
            $acrValue,
            $scopes,
            $requestUri
        );
    }
}
