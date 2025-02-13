<?php

declare(strict_types=1);

namespace Unnits\BankId;

use Stringable;
use Symfony\Component\Uid\Uuid;
use Unnits\BankId\Enums\AcrValue;
use Unnits\BankId\Enums\AuthorizationUriQueryParameter as QueryParam;
use Unnits\BankId\Enums\CodeChallengeMethod;
use Unnits\BankId\Enums\ResponseType;
use Unnits\BankId\Enums\Scope;

final class AuthorizationUri implements Stringable
{
    /**
     * @param string $baseUri
     * @param string $clientId
     * @param string $redirectUri
     * @param string $state
     * @param string|null $nonce
     * @param string|null $bankId
     * @param ResponseType $responseType
     * @param CodeChallengeMethod $codeChallengeMethod
     * @param AcrValue $acrValue
     * @param Scope[] $scopes
     * @param string|null $requestUri
     */
    public function __construct(
        private readonly string $baseUri,
        private readonly string $clientId,
        private readonly string $redirectUri,
        private readonly string $state,
        private readonly ?string $bankId = null,
        private readonly ResponseType $responseType = ResponseType::Code,
        private readonly CodeChallengeMethod $codeChallengeMethod = CodeChallengeMethod::Plain,
        private readonly AcrValue $acrValue = AcrValue::LOA2,
        private readonly array $scopes = [Scope::OpenId],
        private readonly ?string $requestUri = null,
        private readonly ?string $nonce = null,
    ) {
        //
    }

    public function __toString(): string
    {
        $scopes = implode(' ', array_map(
            fn (Scope $scope): string => $scope->value,
            $this->scopes,
        ));

        $params = http_build_query([
            QueryParam::ApprovalPrompt->value => 'auto',
            QueryParam::Scope->value => $scopes,
            QueryParam::CodeChallengeMethod->value => $this->codeChallengeMethod->value,
            QueryParam::ResponseType->value => $this->responseType->value,
            QueryParam::AcrValue->value => $this->acrValue->value,
            QueryParam::State->value => $this->state,
            QueryParam::BankId->value => $this->bankId,
            QueryParam::ClientId->value => $this->clientId,
            QueryParam::RedirectUri->value => $this->redirectUri,
            QueryParam::RequestUri->value => $this->requestUri,
            QueryParam::Nonce->value => $this->nonce,
        ]);

        return sprintf(
            '%s/auth?%s',
            trim($this->baseUri, '/'),
            $params,
        );
    }
}
